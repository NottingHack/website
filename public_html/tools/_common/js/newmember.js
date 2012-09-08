$(document).ready(function() {
	$('input.sysbox').bind('click', checkSysbox);
	
	$('input.sysbox').each(checkSysbox);
});

function checkSysbox() {
	var sID = '#' + $(this).attr('ID') + '-extra';
	if ($(this).attr('checked')) {
		$(sID).slideDown();
	}
	else {
		$(sID).slideUp();
	}
}
