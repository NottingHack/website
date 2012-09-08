/* Crossfade code adapted from http://http://www.simonbattersby.com/blog/simple-jquery-image-crossfade/ */
var oTimer;
var iTimeMove = 7000;
var iTimeFade = 1500;
var iTimeSlide = 600;

var iTop;

var bClick = true;

$(document).ready(function(){
	$("#banner_nav a").bind('click', changeImage);
	
	/* set highlight for current */
	iTop = $('#news ul').position().top + 10;
	$("#news #highlight").css("top", iTop).show();
	
	oTimer = setTimeout("nextImage()", iTimeMove);
})

function nextImage(){
	var oNext = ($('#banner_imgs .active').next().length > 0) ? $('#banner_imgs .active').next() : $('#banner_imgs a:first');
	fadeTo(oNext);
}

function changeImage() {
	if (bClick == true) {
		var sClasses = $(this).attr("class");
		if (sClasses.search(/(banner\d)/) >= 0) {
			oNext = $('#banner_imgs .' + RegExp.$1).parent();
			fadeTo(oNext);
		}
	}
	return false;
}

function fadeTo(oNext) {
	// Clear any exisitng timeouts
	clearTimeout(oTimer);
	bClick = false;
	
	var oActive = $('#banner_imgs .active');
	var sNextClass = oNext.children("img").attr("class");
	var iY = iTop + ((parseInt(sNextClass.charAt(6)) - 1) * 70);
	
	oNext.css('z-index',2);//move the next image up the pile
	
	// move the news highlight
	$("#news #highlight").animate({top: iY}, iTimeFade);
	
	$("#banner_text").slideUp(iTimeSlide, function() {
		oActive.fadeOut(iTimeFade,function(){//fade out the top image
			oActive.css('z-index',1).show().removeClass('active');//reset the z-index and unhide the image
			oNext.css('z-index',3).addClass('active');//make the next image the top one
			
			// set the banner text
			$("#banner_text").html(oNext.children("span").html()).slideDown(iTimeSlide);
			
			bClick = true;
			oTimer = setTimeout("nextImage()", iTimeMove);
		});
	});
	$("#banner_nav a").removeClass("active").filter("." + sNextClass).addClass("active");
}
