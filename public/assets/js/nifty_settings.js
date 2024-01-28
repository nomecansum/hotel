
// Create custom events for radios to enable fire events when they go unchecked.
// ----------------------------------------------
const radioEvent = new Event( "changed" );

[...document.querySelectorAll( "#_dm-settingsContainer input[type='radio']" )].map(( thisRadio ) => {
    thisRadio.previous = thisRadio.checked;
    thisRadio.addEventListener( "transitionend", (e) => {
        if ( e.propertyName == "background-color" && thisRadio.previous != thisRadio.checked ) {
            thisRadio.previous = thisRadio.checked;
            e.target.dispatchEvent( radioEvent );
        }
    })
});


function notificar_cambios(data){
    fetch('/users/tema', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {
            'Content-type': 'application/json; charset=UTF-8'
        }
    })
    .then(response => response.json())
    .then(json => {
        console.log(json);
    });
}





if ( document.getElementById( "_dm-boxedBgContent" ) ) {

    // BOXED LAYOUT WITH BACKGROUND IMAGE
    // ----------------------------------------------
    // HINT : Add the background image to the body or override the $boxed-layout-bg-image SCSS Variable.

    const boxedImgThumbs = [... document.querySelectorAll( "._dm-boxbg__thumb" )];

    boxedImgThumbs.map( ( boxedImgThumb ) => {
        boxedImgThumb.addEventListener( "click", (e) => {
            e.preventDefault();
            if (boxedImgThumb.classList.contains( ".active" )) return;


            let oldImg = document.querySelector( "._dm-boxbg__thumb.active " )
            if ( oldImg ) oldImg.classList.remove( "active" );
            boxedImgThumb.classList.add( "active" );


            let targetIMG = boxedImgThumb.querySelector( "img" ).getAttribute( "src" ).replace( "thumbs", "bg" );
            body.style.backgroundImage = `url( ${targetIMG} )`;

            notificar_cambios({boximg: `url( ${targetIMG} )`});

        });
    });

}





if ( document.getElementById( "_dm-settingsContainer" ) ) {


    // BOXED LAYOUT
    // ----------------------------------------------
    // HINT : Toggle the .boxed-layout class on BODY

    const boxedBgBtn = document.getElementById( "_dm-boxedBgBtn" );
    const boxedBgOption = document.getElementById( "_dm-boxedBgOption" );

    document.getElementById( "_dm-boxedLayoutRadio" ).addEventListener( "changed", (e) => {

        if (e.target.checked && !body.classList. contains( "boxed-layout" )) {

            // Set the current layout to Box mode
            body.classList.add( "boxed-layout" );

            // Enable the background images option
            boxedBgOption.classList.remove( "opacity-50" );
            boxedBgBtn.removeAttribute( "disabled" );
            
            estilo="boxed-layout";
            

        } else {

            // Remove boxed layout
            body.classList.remove( "boxed-layout" );
            body.removeAttribute("style");

            // Disable the background images option
            boxedBgOption.classList.add( "opacity-50" );
            boxedBgBtn.setAttribute( "disabled", true );
            estilo="0";
        }

        notificar_cambios({layout: estilo});

    });





    // CENTERED LAYOUT
    // ----------------------------------------------
    // HINT : Toggle the .centered-layout class on BODY

    document.getElementById( "_dm-centeredLayoutRadio" ).addEventListener( "changed", () => {

        // Set the current layout to Center Mode.
        body.classList.toggle( "centered-layout" );
        notificar_cambios({layout: "centered-layout"});

    });



    // STICKY NAVIGATION
    // ----------------------------------------------
    // HINT : Toggle the .mn--sticky class on #root element.

    document.getElementById( "_dm-stickyNavCheckbox" ).addEventListener( "change", () => {

        // Toggle the sticky navigation class.
        root.classList.toggle( "mn--sticky" );

        notificar_cambios({menu_sticky: "mn--sticky"});

    });




    // MINI NAVIGATION MODE
    // ----------------------------------------------
    // HINT : Toggle the .mn--min class on #root element.

    document.getElementById( "_dm-miniNavRadio" ).addEventListener( "changed", () => {
        // Set the navigation to Mini Mode.
        root.classList.remove( "mn--min", "mn--max","mn--push","mn--slide","mn--reveal" );
        root.classList.toggle( "mn--min" );

        notificar_cambios({menu: "mn--min"});

    });





    // MAXI NAVIGATION MODE
    // ----------------------------------------------
    // HINT : Toggle the .mn--max class on #root element.

    document.getElementById( "_dm-maxiNavRadio" ).addEventListener( "changed", () => {
        // Set the navigation to Maxi Mode.
        root.classList.remove( "mn--min", "mn--max","mn--push","mn--slide","mn--reveal" );
        root.classList.toggle( "mn--max" );

        notificar_cambios({menu: "mn--max"});
    });





    // REMOVE MIN AND MAX CLASSES
    // ----------------------------------------------
    const removeMinMaxNavigation = (e) => {
        if( !e ) return;
        root.classList.remove( "mn--min", "mn--max" );
        buildNav();
    }





    // PUSH NAVIGATION MODE
    // ----------------------------------------------
    // HINT : Toggle the .mn--push class on #root element.

    document.getElementById( "_dm-pushNavRadio" ).addEventListener( "changed", (e) => {


        // Make sure your navigation doesn't have any mini-or max classes.
        removeMinMaxNavigation( e.target.checked );


        // Set the navigation to Push Mode.
        root.classList.remove( "mn--min", "mn--max","mn--push","mn--slide","mn--reveal" );
        root.classList.toggle( "mn--push" );
        notificar_cambios({menu: "mn--push"});

    });





    // SLIDE NAVIGATION MODE
    // ----------------------------------------------
    // HINT : Toggle the .mn--slide class on #root element.

    document.getElementById( "_dm-slideNavRadio" ).addEventListener( "changed", (e) => {


        // Make sure your navigation doesn't have any mini-or max classes.
        removeMinMaxNavigation( e.target.checked );


        // Set the navigation to Slide on Top Mode.
        root.classList.remove( "mn--min", "mn--max","mn--push","mn--slide","mn--reveal" );
        root.classList.toggle( "mn--slide" );
        notificar_cambios({menu: "mn--slide"});

    });





    // REVEAL NAVIGATION MODE
    // ----------------------------------------------
    // HINT : Toggle the .mn--reveal class on #root element.

    document.getElementById( "_dm-revealNavRadio" ).addEventListener( "changed", (e) => {


        // Make sure your navigation doesn't have any mini-or max classes.
        removeMinMaxNavigation( e.target.checked );


        // Set the navigation to Reveal Mode.
        root.classList.remove( "mn--min", "mn--max","mn--push","mn--slide","mn--reveal" );
        root.classList.toggle( "mn--reveal" );
        notificar_cambios({menu: "mn--reveal"});

    });





    // COLOR SCHEMES
    // ----------------------------------------------

    const themeColorsBtn = [...document.querySelectorAll( "._dm-themeColors" )];
    let bootstrapLinkEl  = niftyLinkEl = null;
    const contentHeader  = document.querySelector( ".content__header" );



    // Get the link tag element.
    const getLinkTag = (_type) => {
        //if ( _type == "bootstrap" && bootstrapLinkEl ) return bootstrapLinkEl;
        //else if ( _type == "nifty" && niftyLinkEl ) return niftyLinkEl;

        let targetLink = null;
        [...document.getElementsByTagName("link")].map( (_link) => ( _link.href.includes( _type ) )? targetLink = _link:null );
        ( _type == "bootstrap" ) ? bootstrapLinkEl = targetLink: niftyLinkEl = targetLink;

        return targetLink;
    }

    const defaultBsUrl      = getLinkTag( "bootstrap." ).getAttribute( "href" );
    const defaultNiftyUrl   = getLinkTag( "nifty." ).getAttribute( "href" );
    //const assetsPath        = defaultBsUrl.substring(0, defaultBsUrl.indexOf("/css"))
    const assetsPath        = defaultBsUrl.match( /^.*?assets/g ).toString();
    let stylesLoaded        = 0;
    let totalStyles         = 1;

    const createLinkEl = ( href ) => {
        const newLink = document.createElement( "link" );
        newLink.setAttribute( "rel", "stylesheet" );
        newLink.setAttribute( "href", href );
        newLink.addEventListener( "load", removeLoadingScreen, {
            once: true
        });

        return newLink;
    }



    // Generate a URL for the current scheme selected by the user.
    const makeURL = ( _dir, _file ) => {
        let _uri = "/color-schemes/";
        let newPath = `${ assetsPath }/css${ _uri }${ _dir }/${ _file }.min.css`;
        /*console.log( "Bs path : ", defaultBsUrl );
        console.log( "New path : ", newPath );
        console.log( "Assets path : ", assetsPath );
        console.log( "==================================" );*/
        if ( _dir.length <= 0 ) _file == "bootstrap" ? newPath = defaultBsUrl : newPath = defaultNiftyUrl;

        //console.log(newPath);
        


        return newPath;
    }


    // Remove the loading screen.
    const removeLoadingScreen = () => {
        stylesLoaded ++;
        if ( stylesLoaded < totalStyles ) return;

        // Hide the settings panel
        //bootstrap.Offcanvas.getInstance( "#_dm-settingsContainer" ).hide();

        // Remove the loading screen
        body.classList.remove( "_dm-load-scheme-css" );
        const ldSc = document.querySelector( "#_dm-loading-screen" );
        if( ldSc ) ldSc.remove();

    }

    themeColorsBtn.map( (colorsBtn) => {
        colorsBtn.addEventListener( "click", (e) => {

            if ( colorsBtn.classList.contains( "active" ) ) return;
            e.preventDefault();

            const rootClass        = colorsBtn.getAttribute( "data-hd" );
            const currentActiveBtn = document.querySelector("._dm-themeColors.active");
            let currentBsCSS       = getLinkTag("bootstrap").getAttribute( "href" );
            let newBsCSS           = defaultBsUrl;

            // Reset t0 default value
            stylesLoaded           = 0;
            totalStyles            = 1;


            if ( !colorsBtn.getAttribute( "data-single" ) ) newBsCSS = makeURL( colorsBtn.getAttribute( "data-dir" ), "bootstrap" );

            if ( currentBsCSS !=  newBsCSS ) {

                totalStyles ++;

                if ( !document.getElementById( "_dm-customLoadScreen" ) )
                document.head.insertAdjacentHTML("beforeend", `<style id="_dm-customLoadScreen">._dm-load-scheme-css>._dm-loading-screen{align-items:center;background-color:#fff;color:#2b2c2d;display:flex;flex-direction:column;inset:0;justify-content:center;position:fixed}._dm-load-scheme-css>._dm-loading-screen:before{animation-duration:1s;animation-iteration-count:infinite;animation-name:_dm-spin;animation-timing-function:linear;color:#28292b;content:"\u2686";display:block;font-family:Arial;font-size:5rem;height:2ex;line-height:1;opacity:.1;width:2ex;transform-origin:center center}._dm-load-scheme-css>._dm-loading-screen:after{content:"Please wait while loading . . .";font-family:Poppins,"Open Sans",system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans","Liberation Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";font-size:1rem;font-weight:700;line-height:1.5;margin-top:2rem}._dm-load-scheme-css>:not(._dm-loading-screen){opacity:0;pointer-events:none;visibility:none}@keyframes _dm-spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}</style>`);

                // Add a loading screen
                const loadingScreen = document.createElement("div");
                loadingScreen.setAttribute("id", "_dm-loading-screen");
                loadingScreen.classList.add("_dm-loading-screen");
                document.body.append(loadingScreen);
                document.body.classList.add("_dm-load-scheme-css");

                // Create a new bootstrap link element
                const newBootstrap = createLinkEl( newBsCSS );
                getLinkTag( "bootstrap." ).parentNode.replaceChild( newBootstrap, getLinkTag( "bootstrap." ));
            }

            const newNifty = createLinkEl( makeURL(colorsBtn.getAttribute("data-dir"), "nifty") );
            //getLinkTag("nifty.").setAttribute("href", makeURL(colorsBtn.getAttribute("data-dir"), "nifty"));
            getLinkTag( "nifty." ).parentNode.replaceChild( newNifty, getLinkTag( "nifty." ));


            root.classList.remove( "hd--expanded", "hd--fair" );
            if ( contentHeader ) contentHeader.classList.remove( "border-radius-start" );

            if ( rootClass != null ) {
                rootClass.split( "," ).map( ( _class ) => {
                    if ( _class == "border" ) contentHeader.classList.add( "border-radius-start" );
                    else root.classList.add( `hd--${ _class }` );
                });
                console.log( rootClass );
            }

            notificar_cambios({
                tema: "/color-schemes/"+colorsBtn.getAttribute( "data-dir" ),
                rootClass: rootClass
            });


            // Toggle sctive class for the scheme button.
            if ( currentActiveBtn ) currentActiveBtn.classList.remove( "active" );
            colorsBtn.classList.add( "active" );
        });
    } );

}