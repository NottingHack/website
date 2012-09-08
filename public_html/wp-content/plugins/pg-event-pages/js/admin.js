jQuery(document).ready(function() {
	jQuery(".pgeactions a").bind("click", pgConfirmAction);
});

function pgConfirmAction() {
	var sAction = jQuery(this).attr("href");
	
	if (sAction.indexOf("confirmattendee") >= 0) {
		return( confirm("Really confirm attendee?") );
	}
	else if (sAction.indexOf("deleteattendee") >= 0) {
		return( confirm("Really delete attendee?") );
	}
	else if (sAction.indexOf("confirmreview") >= 0) {
		return( confirm("Really confirm comment and rating?") );
	}
	else if (sAction.indexOf("deletereview") >= 0) {
		return( confirm("Really delete comment and rating?") );
	}
}
