var bChangeStars = true;

jQuery(document).ready(function() {
	jQuery('a#pge-link-attendee').bind('click', addAttendee);
	jQuery('#pge-form-attendee').bind('submit', checkAttendee);
	jQuery('#pge-att-cancel').bind('click', cancelAttendee);
	
	jQuery('a#pge-link-comment').bind('click', addReview);
	jQuery('#pge-form-comment').bind('submit', checkReview);
	jQuery('#pge-com-cancel').bind('click', cancelReview);
	
	jQuery('a.pge-rate-star').bind('mouseover', showStars);
	jQuery('a.pge-rate-star').bind('mouseout', hideStars);
	jQuery('a.pge-rate-star').bind('click', setStars);
});

function addAttendee() {
	jQuery('#pge-overlay').show();
	jQuery('#pge-form-attendee').show();
	
	return false;
}

function cancelAttendee() {
	jQuery("#pge-form-attendee #yourname").removeClass("error");
	jQuery("#pge-form-attendee #youremail").removeClass("error");
	
	jQuery('#pge-form-attendee input:not(.button)').each(function() {
		jQuery(this).val("");
	});
	
	jQuery('#pge-overlay').hide();
	jQuery('#pge-form-attendee').hide();
}

function checkAttendee() {
	bError = false;
	
	jQuery("#pge-form-attendee #yourname").removeClass("error");
	jQuery("#pge-form-attendee #youremail").removeClass("error");
	
	if (!isText(jQuery("#pge-form-attendee #yourname").val())) {
		bError = true;
		jQuery("#pge-form-attendee #yourname").addClass("error");
	}
	if (!isEmail(jQuery("#pge-form-attendee #youremail").val())) {
		bError = true;
		jQuery("#pge-form-attendee #youremail").addClass("error");
	}
	
	if (bError) {
		return false;
	}
	else {
		return true;
	}
}

function addReview() {
	jQuery('#pge-overlay').show();
	jQuery('#pge-form-comment').show();
	
	return false;
}

function cancelReview() {
	jQuery("#pge-form-comment #yourname").removeClass("error");
	jQuery("#pge-form-comment #youremail").removeClass("error");
	
	jQuery('#pge-form-comment input:not(.button)').each(function() {
		jQuery(this).val("");
	});
	
	jQuery('#pge-overlay').hide();
	jQuery('#pge-form-comment').hide();
}

function checkReview() {
	bError = false;
	
	jQuery("#pge-form-comment #yourname").removeClass("error");
	jQuery("#pge-form-comment #youremail").removeClass("error");
	jQuery("#pge-form-comment #rating").removeClass("error");
	
	if (!isText(jQuery("#pge-form-comment #yourname").val())) {
		bError = true;
		jQuery("#pge-form-comment #yourname").addClass("error");
	}
	if (!isEmail(jQuery("#pge-form-comment #youremail").val())) {
		bError = true;
		jQuery("#pge-form-comment #youremail").addClass("error");
	}
	if (jQuery("#pge-form-comment #rating").val() == "-") {
		bError = true;
		jQuery("#pge-form-comment #rating").addClass("error");
	}
	
	if (bError) {
		return false;
	}
	else {
		return true;
	}
	
	return false;
}

function showStars() {
	if (bChangeStars) {
		var iRating = parseInt($(this).attr("id").replace("pgerate", ""));
		for (var i = 1; i <= iRating; i++) {
			$("#pgerate" + i).css( {backgroundPosition: "0 -19px"} );
		}
	}
}

function hideStars() {
	if (bChangeStars) {
		jQuery('a.pge-rate-star').css( {backgroundPosition: "0 0"} );
	}
	else {
		bChangeStars = true;
	}
}

function setStars() {
	bChangeStars = false;
	var iRating = parseInt($(this).attr("id").replace("pgerate", ""));
	$("#pge-rating").val(iRating);
	return false;
}

function isName(text) {
	if (typeof(text) == "undefined") {
		return false;
	}
	else if (!/^[a-zA-Z0-9 \.'-]+$/.test(text)) {
		return false;
	}
	else {
		return true;
	}
}

function isText(text) {
	if (typeof(text) == "undefined") {
		return false;
	}
	else if (!/^[a-zA-Z0-9\xC0-\xFF \'\.\,\?\!\"\%\£\&\(\)\-\/\+\=\<\>\s]+$/.test(text)) {
		return false;
	}
	else {
		return true;
	}
}

function isMultiText(text) {
	if (typeof(text) == "undefined") {
		return false;
	}
	else if (!/^[a-zA-Z0-9\xC0-\xFF \'\.\,\?\!\"\%\£\&\(\)\-\/\+\=\<\>\s\n]+$/.test(text)) {
		return false;
	}
	else {
		return true;
	}
}

function isEmail(email) {
	if (typeof(email) == "undefined") {
		return false;
	}
	else if (!/^[a-zA-Z0-9\.-]+\@[a-zA-Z0-9]+\.[a-zA-Z\.]+$/i.test(email)) {
		return false;
	}
	else {
		return true;
	}
}
