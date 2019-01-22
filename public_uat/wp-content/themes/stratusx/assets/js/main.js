"use strict";
/**
 * General Custom JS Functions
 *
 * @author     Themovation <themovation@gmail.com>
 * @copyright  2014 Themovation INC.
 * @license    http://themeforest.net/licenses/regular
 * @version    1.1
 */

/*
 # Helper Functions
 # On Window Resize
 # On Window Load
 */

//======================================================================
// Helper Functions
//======================================================================

//-----------------------------------------------------
// NAVIGATION - Adds support for Mobile Navigation
// Detect screen size, add / subtract data-toggle
// for mobile dropdown menu.
//-----------------------------------------------------	
function themo_support_mobile_navigation(){
	
	// If mobile navigation is active, add data attributes for mobile touch / toggle
	if (Modernizr.mq('(max-width: 767px)')) {
		//console.log('Adding data-toggle, data-target');
		jQuery("li.dropdown .dropdown-toggle").attr("data-toggle", "dropdown");
		jQuery("li.dropdown .dropdown-toggle").attr("data-target", "#");
	}
	
	// If mobile navigation is NOT active, remove data attributes for mobile touch / toggle
	if (Modernizr.mq('(min-width:768px)')) {
		//console.log('Removing data-toggle, data-target');
		jQuery("li.dropdown .dropdown-toggle").removeAttr("data-toggle", "dropdown");
		jQuery("li.dropdown .dropdown-toggle").removeAttr("data-target", "#");
	}
}


//-----------------------------------------------------
// Detect if touch device via modernizr, return true
//-----------------------------------------------------	
function themo_is_touch_device(checkScreenSize){

	if (typeof checkScreenSize === "undefined" || checkScreenSize === null) { 
    	checkScreenSize = true; 
	}

	var deviceAgent = navigator.userAgent.toLowerCase();
 

    var isTouch = (deviceAgent.match(/(iphone|ipod|ipad)/) ||
		deviceAgent.match(/(android)/)  || 
		deviceAgent.match(/iphone/i) || 
		deviceAgent.match(/ipad/i) || 
		deviceAgent.match(/ipod/i) || 
		deviceAgent.match(/blackberry/i));
	
	if(checkScreenSize){
		var isMobileSize = Modernizr.mq('(max-width:767px)');
	}else{
		var isMobileSize = false;
	}
	
	if(isTouch || isMobileSize ){
		return true;
	}

	return false;
}


//-----------------------------------------------------
// Disable Transparent Header for Mobile
//-----------------------------------------------------
function themo_no_transparent_header_for_mobile(isTouch){
	
	if (jQuery(".navbar[data-transparent-header]").length) {
		if(isTouch){ 
			jQuery('.navbar').attr("data-transparent-header", "false");		
		}
		else{
			jQuery('.navbar').attr("data-transparent-header", "true");		
		}
	}
}





//-----------------------------------------------------
// Scroll Up
//-----------------------------------------------------
function themo_start_scrollup() {
	
	jQuery.scrollUp({
		animationSpeed: 200,
		animation: 'fade',
		scrollSpeed: 500,
		scrollImg: { active: true, type: 'background', src: '../../images/top.png' }
	});
}



var nice = false;

/**
 * Protect window.console method calls, e.g. console is not defined on IE
 * unless dev tools are open, and IE doesn't define console.debug
 */
(function() {

	if (!window.console) {
		window.console = {};
	}
	// union of Chrome, FF, IE, and Safari console methods
	var m = [
		"log", "info", "warn", "error", "debug", "trace", "dir", "group",
		"groupCollapsed", "groupEnd", "time", "timeEnd", "profile", "profileEnd",
		"dirxml", "assert", "count", "markTimeline", "timeStamp", "clear"
	];
	// define undefined methods as noops to prevent errors
	for (var i = 0; i < m.length; i++) {
		if (!window.console[m[i]]) {
			window.console[m[i]] = function() {};
		}
	}
})();

//======================================================================
// Executes when HTML-Document is loaded and DOM is ready
//======================================================================
jQuery(document).ready(function($) {
	"use strict";

    // Preloader : Is really only used for the flexslider but is added to the body tag.
    // If flex is detected, we put a timeout on it (5s( so it does not get stuck spinning.
    // If no flex, then disable.
    if (jQuery("#main-flex-slider")[0]){
        // Do nothing / flex will figure it out.
        setTimeout(function(){
            jQuery('body').addClass('loaded');
        }, 10000);
    }else{
        jQuery('body').addClass('loaded');
    }

    // add body class for touch devices.
    if (themo_is_touch_device()) {
        jQuery('body').addClass('th-touch');
    }

	// Add support for mobile navigation
	themo_support_mobile_navigation($);

    // Support for sub menu navigation / also works with sticky header.

    jQuery("body").on("click", "ul.dropdown-menu .dropdown-submenu > a", function(event){
        //console.log($(this).text());
        event.preventDefault();
        event.stopPropagation();
        jQuery(this).parents('ul.dropdown-menu .dropdown-submenu').toggleClass('open');
    });

    // Sticky Header - Set options
    var options = {
        // Scroll offset. Accepts Number or "String" (for class/ID)
        offset: 125, // OR — offset: '.classToActivateAt',

        classes: {
            clone:   'headhesive--clone',
            stick:   'headhesive--stick',
            unstick: 'headhesive--unstick'
        }
    };
    try
    {
        // Initialise with options
        var banner = new Headhesive('body.th-sticky-header .banner', options);
        jQuery('body.th-sticky-header').addClass('headhesive');
    }
    catch (err) {
        console.log('Sticky header deactivated. WP Dash / Appearance / Customize / Theme Options / Menu & Header');
    }

    // Close sticky header on menu item click.
    jQuery('.navbar-collapse a:not(.dropdown-toggle)').live( "click", function() {
        jQuery('.navbar-collapse').css('height', '0');
        jQuery('.navbar-collapse').removeClass('in');
    });

    /**
     * Check a href for an anchor. If exists, and in document, scroll to it.
     * If href argument ommited, assumes context (this) is HTML Element,
     * which will be the case when invoked by jQuery after an event
     */
    function scroll_if_anchor(href) {
        href = typeof(href) == "string" ? href : jQuery(this).attr("href");

        var fromTop = 0;
        if (jQuery("header").hasClass("headhesive--clone")) {
            fromTop = jQuery(".headhesive--clone").height() ;
        }

        // You could easily calculate this dynamically if you prefer
        //var fromTop = 50;

        // If our Href points to a valid, non-empty anchor, and is on the same page (e.g. #foo)
        // Legacy jQuery and IE7 may have issues: http://stackoverflow.com/q/1593174
        if(href.indexOf("#") == 0) {
            var $target = jQuery(href);

            // Older browser without pushState might flicker here, as they momentarily
            // jump to the wrong position (IE < 10)
            if($target.length) {
                //console.log('STRATUS Anchor detected - Scroll  ' + $target.offset().top);
                //console.log('STRATUS Anchor detected - Offset ' + fromTop);
                jQuery('html, body').animate({ scrollTop: $target.offset().top - fromTop }, 500, 'linear', function() {
                    //alert("Finished animating");
                });
                if(history && "pushState" in history) {
                    history.pushState({}, document.title, window.location.pathname + href);
                    return false;
                }
            }
        }
    }

    // When our page loads, check to see if it contains and anchor
    scroll_if_anchor(window.location.hash);


    // Detect and set isTouch for touch screens
    //	var isTouch = themo_is_touch_device();

    // Set off set for waypoints
    //if(!isTouch){
        //Setup waypoints plugin

        var th_offset = 0;
        if (jQuery("header").hasClass("headhesive--clone")) {
            th_offset = jQuery(".headhesive--clone").height() ;
        }

        // Add space for Elementor Menu Anchor link
        jQuery( window ).on( 'elementor/frontend/init', function() {
            elementorFrontend.hooks.addFilter( 'frontend/handlers/menu_anchor/scroll_top_distance', function( scrollTop ) {
            	//console.log('ELEM HOOK - Scroll offset ' + th_offset);
                return scrollTop - th_offset;
            } );
        } );
    //}


	if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
		console.log('Smooth Scroll Off (Safari).');
	}else{
		try 
		{
			// Initialise with options
			nice = jQuery("html").niceScroll({
			zindex:20000,
			scrollspeed:60,
			mousescrollstep:60,
			cursorborderradius: '10px', // Scroll cursor radius
			cursorborder: '1px solid rgba(255, 255, 255, 0.4)',
			cursorcolor: 'rgba(0, 0, 0, 0.6)',     // Scroll cursor color
			//autohidemode: 'true',     // Do not hide scrollbar when mouse out
			cursorwidth: '10px',       // Scroll cursor width
			autohidemode: false,
			
				});
		} 
		catch (err) {
			console.log('Smooth Scroll Off.');
		}
	}

});


//======================================================================
// On Window Load - executes when complete page is fully loaded, including all frames, objects and images
//======================================================================
 jQuery(window).load(function($) {
	 "use strict";

	// Detect and set isTouch for touch screens
	var isTouch = themo_is_touch_device();

	// Disable Transparent Header for Mobile / touch
	themo_no_transparent_header_for_mobile(isTouch);

	// Start Scroll Up
	themo_start_scrollup();



	
});
 
//======================================================================
// On Window Resize
//======================================================================
 jQuery(window).resize(function($){
	 "use strict";
	// Detect and set isTouch for touch screens
	var isTouch = themo_is_touch_device();

	// Add support for mobile navigation
	themo_support_mobile_navigation();

	// Disable Transparent Header for Mobile / touch
	themo_no_transparent_header_for_mobile(isTouch);
});


