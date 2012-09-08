var iCurImg = 0;
var bStart = false;
var oTimer;

$(document).ready(function() {
	$("#nh-ind li").supersleight();
	
	$("#nh-ind a").bind('click', clickMove);
	
	// we add on the irst image to the end, for some tricks later!
	var iWidth = (iNumImgs+1) * 620;
	$("#nh-slider").css("width", iWidth);
	$("#nh-slider img:eq(0)").clone().appendTo("#nh-slider");
	
	var iIndWidth = iNumImgs * 20;
	$("div#nh-ind ul").css("width", iIndWidth);
	
	oTimer = setTimeout("nextImage()", iTimeMove);
});

function clickMove() {
	var sID = $(this).parent().attr("id");
	var iImg = parseInt(sID.replace("nh-ind-", ""));
	
	if (iImg >= 0 && iImg < iNumImgs) {
		moveToImage(iImg);
	}
	
	return false;
}

function nextImage() {
	iCurImg++
	
	bStart = false;
	iNextImg = iCurImg;
	if (iCurImg >= iNumImgs) {
		iCurImg = 0;
		iNextImg = iNumImgs;
		bStart = true;
	}
	slideTo(iNextImg);
}

function moveToImage(iImg) {
	if (iImg < iNumImgs) {
		iCurImg = iImg;
		slideTo(iImg);
	}
}

function slideTo(iImg) {
	// Clear any exisitng timeouts
	clearTimeout(oTimer);
	
	var iPos = (iImg * 620) * -1;
	// make it look as if 1st image is now at the end!
	
	$("#nh-slider").animate({
		left: iPos
		}, iTimeAnim, function() {
			// and now magically go to the beginning again!
			if (bStart) {
				$("#nh-slider").css("left", 0);
			}
			
			$("#nh-ind li").removeClass("on");
			$("#nh-ind-" + iCurImg).addClass("on");
			
			$("#nh-text h2").html(aTexts[iCurImg].title);
			
			$("#nh-text div").html("<p>" + aTexts[iCurImg].paras.join("</p><p>") + "</p>");
			
			$("#nh-text p.cta a").html(aTexts[iCurImg].link).attr("href", aTexts[iCurImg].href);
			if (aTexts[iCurImg].type == "external") {
				$("#nh-text p.cta a").html(aTexts[iCurImg].link).attr("target", "_blank");
			}
			else if (aTexts[iCurImg].type == "internal") {
				$("#nh-text p.cta a").html(aTexts[iCurImg].link).attr("target", "_self");
			}
			
			
			oTimer = setTimeout("nextImage()", iTimeMove);
		});
}
