jQuery(document).ready(function() {
	jQuery('div.gl-main-calendar div.gl-event').bind('click', glgcOpenEvent).addClass('clickable');
	jQuery('body').bind('click', glgcCloseEvent);
});

function glgcOpenEvent() {
	var sPosition = jQuery(this).position();
	var iTop = sPosition.top;
	var iLeft = sPosition.left;  
	
	var iMainWidth = jQuery('div.gl-main-calendar').width();
	var iMainHeight = jQuery('div.gl-main-calendar').height();
	
	jQuery('#gl-event-details').remove();
	var sBox = '<div id="gl-event-details">';
	
	sBox += '<div class="gl-ed-title">' + jQuery('.gl-name', this).html() + '</div>';
	sBox += '<div class="gl-ed-close">x</div>';
	
	sBox += '<div class="gl-ed-desc">' + jQuery('.gl-desc', this).html() + '</div>';
	
	if (jQuery('.gl-link', this).length > 0) {
		sBox += '<div class="gl-ed-link"><a href="' + jQuery('.gl-link', this).html() + '">more details ></a></div>';
	}
	
	sBox += '</div>';
	
	jQuery('div.gl-main-calendar').append(sBox);
	jQuery('#gl-event-details div.gl-ed-close').bind('click', glgcCloseEvent).addClass('clickable');
	
	iBoxWidth = jQuery('#gl-event-details').width();
	iBoxHeight = jQuery('#gl-event-details').height();
	
	/* Move the box */
	if (iLeft + iBoxWidth > iMainWidth) {
		iLeft = iMainWidth - iBoxWidth;
	}
	if (iTop + iBoxHeight > iMainHeight) {
		iTop = iMainHeight - iBoxHeight;
	}
	iLeft = iLeft - 10;
	iTop = iTop - 10;
	jQuery('#gl-event-details').css('top', iTop).css('left', iLeft); 
	
	return false;
}

function glgcCloseEvent() {
	jQuery('#gl-event-details').remove();
}
