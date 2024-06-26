"use strict";
/**
 * dd-draggable.ts 5.1.1
 * Copyright (c) 2021-2022 Alain Dumesny - see GridStack root license
 */
var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
exports.DDDraggable = void 0;
var dd_manager_1 = require("./dd-manager");
var dd_utils_1 = require("./dd-utils");
var dd_base_impl_1 = require("./dd-base-impl");
var DDDraggable = /** @class */ (function (_super) {
    __extends(DDDraggable, _super);
    function DDDraggable(el, option) {
        if (option === void 0) { option = {}; }
        var _this = _super.call(this) || this;
        /** @internal */
        _this.dragging = false;
        /** @internal TODO: set to public as called by DDDroppable! */
        _this.ui = function () {
            var containmentEl = _this.el.parentElement;
            var containmentRect = containmentEl.getBoundingClientRect();
            var offset = _this.helper.getBoundingClientRect();
            return {
                position: {
                    top: offset.top - containmentRect.top,
                    left: offset.left - containmentRect.left
                }
                /* not used by GridStack for now...
                helper: [this.helper], //The object arr representing the helper that's being dragged.
                offset: { top: offset.top, left: offset.left } // Current offset position of the helper as { top, left } object.
                */
            };
        };
        _this.el = el;
        _this.option = option;
        // get the element that is actually supposed to be dragged by
        var className = option.handle.substring(1);
        _this.dragEl = el.classList.contains(className) ? el : el.querySelector(option.handle) || el;
        // create var event binding so we can easily remove and still look like TS methods (unlike anonymous functions)
        _this._dragStart = _this._dragStart.bind(_this);
        _this._drag = _this._drag.bind(_this);
        _this._dragEnd = _this._dragEnd.bind(_this);
        _this.enable();
        return _this;
    }
    DDDraggable.prototype.on = function (event, callback) {
        _super.prototype.on.call(this, event, callback);
    };
    DDDraggable.prototype.off = function (event) {
        _super.prototype.off.call(this, event);
    };
    DDDraggable.prototype.enable = function () {
        _super.prototype.enable.call(this);
        this.dragEl.draggable = true;
        this.dragEl.addEventListener('dragstart', this._dragStart);
        this.el.classList.remove('ui-draggable-disabled');
        this.el.classList.add('ui-draggable');
    };
    DDDraggable.prototype.disable = function (forDestroy) {
        if (forDestroy === void 0) { forDestroy = false; }
        _super.prototype.disable.call(this);
        this.dragEl.removeAttribute('draggable');
        this.dragEl.removeEventListener('dragstart', this._dragStart);
        this.el.classList.remove('ui-draggable');
        if (!forDestroy)
            this.el.classList.add('ui-draggable-disabled');
    };
    DDDraggable.prototype.destroy = function () {
        if (this.dragging) {
            // Destroy while dragging should remove dragend listener and manually trigger
            // dragend, otherwise dragEnd can't perform dragstop because eventRegistry is
            // destroyed.
            this._dragEnd({});
        }
        this.disable(true);
        delete this.el;
        delete this.helper;
        delete this.option;
        _super.prototype.destroy.call(this);
    };
    DDDraggable.prototype.updateOption = function (opts) {
        var _this = this;
        Object.keys(opts).forEach(function (key) { return _this.option[key] = opts[key]; });
        return this;
    };
    /** @internal */
    DDDraggable.prototype._dragStart = function (event) {
        var _this = this;
        dd_manager_1.DDManager.dragElement = this;
        this.helper = this._createHelper(event);
        this._setupHelperContainmentStyle();
        this.dragOffset = this._getDragOffset(event, this.el, this.helperContainment);
        var ev = dd_utils_1.DDUtils.initEvent(event, { target: this.el, type: 'dragstart' });
        if (this.helper !== this.el) {
            this._setupDragFollowNodeNotifyStart(ev);
            // immediately set external helper initial position to avoid flickering behavior and unnecessary looping in `_packNodes()`
            this._dragFollow(event);
        }
        else {
            this.dragFollowTimer = window.setTimeout(function () {
                delete _this.dragFollowTimer;
                _this._setupDragFollowNodeNotifyStart(ev);
            }, 0);
        }
        this._cancelDragGhost(event);
    };
    /** @internal */
    DDDraggable.prototype._setupDragFollowNodeNotifyStart = function (ev) {
        this._setupHelperStyle();
        document.addEventListener('dragover', this._drag, DDDraggable.dragEventListenerOption);
        this.dragEl.addEventListener('dragend', this._dragEnd);
        if (this.option.start) {
            this.option.start(ev, this.ui());
        }
        this.dragging = true;
        this.helper.classList.add('ui-draggable-dragging');
        this.triggerEvent('dragstart', ev);
        return this;
    };
    /** @internal */
    DDDraggable.prototype._drag = function (event) {
        // Safari: prevent default to allow drop to happen instead of reverting back (with animation) and delaying dragend #1541
        // https://stackoverflow.com/questions/61760755/how-to-fire-dragend-event-immediately
        event.preventDefault();
        this._dragFollow(event);
        var ev = dd_utils_1.DDUtils.initEvent(event, { target: this.el, type: 'drag' });
        if (this.option.drag) {
            this.option.drag(ev, this.ui());
        }
        this.triggerEvent('drag', ev);
    };
    /** @internal */
    DDDraggable.prototype._dragEnd = function (event) {
        if (this.dragFollowTimer) {
            clearTimeout(this.dragFollowTimer);
            delete this.dragFollowTimer;
            return;
        }
        else {
            if (this.paintTimer) {
                cancelAnimationFrame(this.paintTimer);
            }
            document.removeEventListener('dragover', this._drag, DDDraggable.dragEventListenerOption);
            this.dragEl.removeEventListener('dragend', this._dragEnd);
        }
        this.dragging = false;
        this.helper.classList.remove('ui-draggable-dragging');
        this.helperContainment.style.position = this.parentOriginStylePosition || null;
        if (this.helper === this.el) {
            this._removeHelperStyle();
        }
        else {
            this.helper.remove();
        }
        var ev = dd_utils_1.DDUtils.initEvent(event, { target: this.el, type: 'dragstop' });
        if (this.option.stop) {
            this.option.stop(ev); // Note: ui() not used by gridstack so don't pass
        }
        this.triggerEvent('dragstop', ev);
        delete dd_manager_1.DDManager.dragElement;
        delete this.helper;
    };
    /** @internal create a clone copy (or user defined method) of the original drag item if set */
    DDDraggable.prototype._createHelper = function (event) {
        var _this = this;
        var helper = this.el;
        if (typeof this.option.helper === 'function') {
            helper = this.option.helper(event);
        }
        else if (this.option.helper === 'clone') {
            helper = dd_utils_1.DDUtils.clone(this.el);
        }
        if (!document.body.contains(helper)) {
            dd_utils_1.DDUtils.appendTo(helper, this.option.appendTo === 'parent' ? this.el.parentNode : this.option.appendTo);
        }
        if (helper === this.el) {
            this.dragElementOriginStyle = DDDraggable.originStyleProp.map(function (prop) { return _this.el.style[prop]; });
        }
        return helper;
    };
    /** @internal */
    DDDraggable.prototype._setupHelperStyle = function () {
        var _this = this;
        // TODO: set all at once with style.cssText += ... ? https://stackoverflow.com/questions/3968593
        var rec = this.helper.getBoundingClientRect();
        var style = this.helper.style;
        style.pointerEvents = 'none';
        style['min-width'] = 0; // since we no longer relative to our parent and we don't resize anyway (normally 100/#column %)
        style.width = this.dragOffset.width + 'px';
        style.height = this.dragOffset.height + 'px';
        style.willChange = 'left, top';
        style.position = 'fixed'; // let us drag between grids by not clipping as parent .grid-stack is position: 'relative'
        style.left = rec.left + 'px';
        style.top = rec.top + 'px';
        style.transition = 'none'; // show up instantly
        setTimeout(function () {
            if (_this.helper) {
                style.transition = null; // recover animation
            }
        }, 0);
        return this;
    };
    /** @internal */
    DDDraggable.prototype._removeHelperStyle = function () {
        var _this = this;
        var _a;
        var node = (_a = this.helper) === null || _a === void 0 ? void 0 : _a.gridstackNode;
        // don't bother restoring styles if we're gonna remove anyway...
        if (this.dragElementOriginStyle && (!node || !node._isAboutToRemove)) {
            var helper_1 = this.helper;
            // don't animate, otherwise we animate offseted when switching back to 'absolute' from 'fixed' 
            var transition_1 = this.dragElementOriginStyle['transition'] || null;
            helper_1.style.transition = this.dragElementOriginStyle['transition'] = 'none';
            DDDraggable.originStyleProp.forEach(function (prop) { return helper_1.style[prop] = _this.dragElementOriginStyle[prop] || null; });
            setTimeout(function () { return helper_1.style.transition = transition_1; }, 50); // recover animation from saved vars after a pause (0 isn't enough #1973)
        }
        delete this.dragElementOriginStyle;
        return this;
    };
    /** @internal */
    DDDraggable.prototype._dragFollow = function (event) {
        var _this = this;
        if (this.paintTimer) {
            cancelAnimationFrame(this.paintTimer);
        }
        this.paintTimer = requestAnimationFrame(function () {
            delete _this.paintTimer;
            var offset = _this.dragOffset;
            var containmentRect = { left: 0, top: 0 };
            if (_this.helper.style.position === 'absolute') {
                var _a = _this.helperContainment.getBoundingClientRect(), left = _a.left, top_1 = _a.top;
                containmentRect = { left: left, top: top_1 };
            }
            _this.helper.style.left = event.clientX + offset.offsetLeft - containmentRect.left + 'px';
            _this.helper.style.top = event.clientY + offset.offsetTop - containmentRect.top + 'px';
        });
    };
    /** @internal */
    DDDraggable.prototype._setupHelperContainmentStyle = function () {
        this.helperContainment = this.helper.parentElement;
        if (this.helper.style.position !== 'fixed') {
            this.parentOriginStylePosition = this.helperContainment.style.position;
            if (window.getComputedStyle(this.helperContainment).position.match(/static/)) {
                this.helperContainment.style.position = 'relative';
            }
        }
        return this;
    };
    /** @internal prevent the default ghost image to be created (which has wrong as we move the helper/element instead
     * (legacy jquery UI code updates the top/left of the item).
     * TODO: maybe use mouse event instead of HTML5 drag as we have to work around it anyway, or change code to not update
     * the actual grid-item but move the ghost image around (and special case jq version) ?
     **/
    DDDraggable.prototype._cancelDragGhost = function (e) {
        /* doesn't seem to do anything...
        let t = e.dataTransfer;
        t.effectAllowed = 'none';
        t.dropEffect = 'none';
        t.setData('text', '');
        */
        // NOTE: according to spec (and required by Safari see #1540) the image has to be visible in the browser (in dom and not hidden) so make it a 1px div
        var img = document.createElement('div');
        img.style.width = '1px';
        img.style.height = '1px';
        img.style.position = 'fixed'; // prevent unwanted scrollbar
        document.body.appendChild(img);
        e.dataTransfer.setDragImage(img, 0, 0);
        setTimeout(function () { return document.body.removeChild(img); }); // nuke once drag had a chance to grab this 'image'
        e.stopPropagation();
        return this;
    };
    /** @internal */
    DDDraggable.prototype._getDragOffset = function (event, el, parent) {
        // in case ancestor has transform/perspective css properties that change the viewpoint
        var xformOffsetX = 0;
        var xformOffsetY = 0;
        if (parent) {
            var testEl = document.createElement('div');
            dd_utils_1.DDUtils.addElStyles(testEl, {
                opacity: '0',
                position: 'fixed',
                top: 0 + 'px',
                left: 0 + 'px',
                width: '1px',
                height: '1px',
                zIndex: '-999999',
            });
            parent.appendChild(testEl);
            var testElPosition = testEl.getBoundingClientRect();
            parent.removeChild(testEl);
            xformOffsetX = testElPosition.left;
            xformOffsetY = testElPosition.top;
            // TODO: scale ?
        }
        var targetOffset = el.getBoundingClientRect();
        return {
            left: targetOffset.left,
            top: targetOffset.top,
            offsetLeft: -event.clientX + targetOffset.left - xformOffsetX,
            offsetTop: -event.clientY + targetOffset.top - xformOffsetY,
            width: targetOffset.width,
            height: targetOffset.height
        };
    };
    /** @internal #1541 can't have {passive: true} on Safari as otherwise it reverts animate back to old location on drop */
    DDDraggable.dragEventListenerOption = true; // DDUtils.isEventSupportPassiveOption ? { capture: true, passive: true } : true;
    /** @internal properties we change during dragging, and restore back */
    DDDraggable.originStyleProp = ['transition', 'pointerEvents', 'position',
        'left', 'top', 'opacity', 'zIndex', 'width', 'height', 'willChange', 'min-width'];
    return DDDraggable;
}(dd_base_impl_1.DDBaseImplement));
exports.DDDraggable = DDDraggable;
//# sourceMappingURL=dd-draggable.js.map