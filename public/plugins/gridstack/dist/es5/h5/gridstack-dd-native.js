"use strict";
/**
 * gridstack-dd-native.ts 5.1.1
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
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    Object.defineProperty(o, k2, { enumerable: true, get: function() { return m[k]; } });
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __exportStar = (this && this.__exportStar) || function(m, exports) {
    for (var p in m) if (p !== "default" && !exports.hasOwnProperty(p)) __createBinding(exports, m, p);
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.GridStackDDNative = void 0;
var dd_manager_1 = require("./dd-manager");
var dd_element_1 = require("./dd-element");
var gridstack_dd_1 = require("../gridstack-dd");
var utils_1 = require("../utils");
// export our base class (what user should use) and all associated types
__exportStar(require("../gridstack-dd"), exports);
/**
 * HTML 5 Native DragDrop based drag'n'drop plugin.
 */
var GridStackDDNative = /** @class */ (function (_super) {
    __extends(GridStackDDNative, _super);
    function GridStackDDNative() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    GridStackDDNative.prototype.resizable = function (el, opts, key, value) {
        this._getDDElements(el).forEach(function (dEl) {
            var _a;
            if (opts === 'disable' || opts === 'enable') {
                dEl.ddResizable && dEl.ddResizable[opts](); // can't create DD as it requires options for setupResizable()
            }
            else if (opts === 'destroy') {
                dEl.ddResizable && dEl.cleanResizable();
            }
            else if (opts === 'option') {
                dEl.setupResizable((_a = {}, _a[key] = value, _a));
            }
            else {
                var grid = dEl.el.gridstackNode.grid;
                var handles = dEl.el.getAttribute('gs-resize-handles') ? dEl.el.getAttribute('gs-resize-handles') : grid.opts.resizable.handles;
                dEl.setupResizable(__assign(__assign(__assign({}, grid.opts.resizable), { handles: handles }), {
                    start: opts.start,
                    stop: opts.stop,
                    resize: opts.resize
                }));
            }
        });
        return this;
    };
    GridStackDDNative.prototype.draggable = function (el, opts, key, value) {
        this._getDDElements(el).forEach(function (dEl) {
            var _a;
            if (opts === 'disable' || opts === 'enable') {
                dEl.ddDraggable && dEl.ddDraggable[opts](); // can't create DD as it requires options for setupDraggable()
            }
            else if (opts === 'destroy') {
                dEl.ddDraggable && dEl.cleanDraggable();
            }
            else if (opts === 'option') {
                dEl.setupDraggable((_a = {}, _a[key] = value, _a));
            }
            else {
                var grid = dEl.el.gridstackNode.grid;
                dEl.setupDraggable(__assign(__assign({}, grid.opts.draggable), {
                    containment: (grid.opts._isNested && !grid.opts.dragOut)
                        ? grid.el.parentElement
                        : (grid.opts.draggable.containment || null),
                    start: opts.start,
                    stop: opts.stop,
                    drag: opts.drag
                }));
            }
        });
        return this;
    };
    GridStackDDNative.prototype.dragIn = function (el, opts) {
        this._getDDElements(el).forEach(function (dEl) { return dEl.setupDraggable(opts); });
        return this;
    };
    GridStackDDNative.prototype.droppable = function (el, opts, key, value) {
        if (typeof opts.accept === 'function' && !opts._accept) {
            opts._accept = opts.accept;
            opts.accept = function (el) { return opts._accept(el); };
        }
        this._getDDElements(el).forEach(function (dEl) {
            var _a;
            if (opts === 'disable' || opts === 'enable') {
                dEl.ddDroppable && dEl.ddDroppable[opts]();
            }
            else if (opts === 'destroy') {
                if (dEl.ddDroppable) { // error to call destroy if not there
                    dEl.cleanDroppable();
                }
            }
            else if (opts === 'option') {
                dEl.setupDroppable((_a = {}, _a[key] = value, _a));
            }
            else {
                dEl.setupDroppable(opts);
            }
        });
        return this;
    };
    /** true if element is droppable */
    GridStackDDNative.prototype.isDroppable = function (el) {
        return !!(el && el.ddElement && el.ddElement.ddDroppable && !el.ddElement.ddDroppable.disabled);
    };
    /** true if element is draggable */
    GridStackDDNative.prototype.isDraggable = function (el) {
        return !!(el && el.ddElement && el.ddElement.ddDraggable && !el.ddElement.ddDraggable.disabled);
    };
    /** true if element is draggable */
    GridStackDDNative.prototype.isResizable = function (el) {
        return !!(el && el.ddElement && el.ddElement.ddResizable && !el.ddElement.ddResizable.disabled);
    };
    GridStackDDNative.prototype.on = function (el, name, callback) {
        this._getDDElements(el).forEach(function (dEl) {
            return dEl.on(name, function (event) {
                callback(event, dd_manager_1.DDManager.dragElement ? dd_manager_1.DDManager.dragElement.el : event.target, dd_manager_1.DDManager.dragElement ? dd_manager_1.DDManager.dragElement.helper : null);
            });
        });
        return this;
    };
    GridStackDDNative.prototype.off = function (el, name) {
        this._getDDElements(el).forEach(function (dEl) { return dEl.off(name); });
        return this;
    };
    /** @internal returns a list of DD elements, creating them on the fly by default */
    GridStackDDNative.prototype._getDDElements = function (els, create) {
        if (create === void 0) { create = true; }
        var hosts = utils_1.Utils.getElements(els);
        if (!hosts.length)
            return [];
        var list = hosts.map(function (e) { return e.ddElement || (create ? dd_element_1.DDElement.init(e) : null); });
        if (!create) {
            list.filter(function (d) { return d; });
        } // remove nulls
        return list;
    };
    return GridStackDDNative;
}(gridstack_dd_1.GridStackDD));
exports.GridStackDDNative = GridStackDDNative;
// finally register ourself
gridstack_dd_1.GridStackDD.registerPlugin(GridStackDDNative);
//# sourceMappingURL=gridstack-dd-native.js.map