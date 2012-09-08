jQuery(document).ready(function() {
	jQuery("ul#menu-main-menu > li").bind("mouseover", nh_showSubMenu);
	jQuery("ul#menu-main-menu > li").bind("mouseout", nh_hideSubMenu);
});

function nh_showSubMenu() {
	/* hide all shown menus at the moment */
	jQuery('#access ul ul').hide();
	
	jQuery('ul', this).show();
	$("> a", this).addClass("hovered");
}

function nh_hideSubMenu() {
	/* hide all shown menus at the moment */
	jQuery('#access ul ul').hide();
	$("a", this).removeClass("hovered");
	
	/* Show the correct one, if it is there */
	jQuery('#access li.current_page_ancestor ul').show();
	jQuery('#access li.current_page_parent ul').show();
	jQuery('#access li.current_page_item ul').show();
	jQuery('#access li.current-page-ancestor ul').show();
}
