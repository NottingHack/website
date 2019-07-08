<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}


class AppleTouchIconHooks {
    /* Static Methods */

    /**
    * BeforePageDisplay hook
    *
    * @param &$out
    * @param &$skin
    */
    public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin){
        $out->addHeadItem( "icon", "<link rel=\"apple-touch-icon\" href=\"/apple-touch-icon.png\">");
        $out->addHeadItem( "icon-60", "<link rel=\"apple-touch-icon\" sizes=\"60x60\" href=\"/apple-touch-icon-60x60.png\">");
        $out->addHeadItem( "icon-76", "<link rel=\"apple-touch-icon\" sizes=\"76x76\" href=\"/apple-touch-icon-76x76.png\">");
        $out->addHeadItem( "icon-120", "<link rel=\"apple-touch-icon\" sizes=\"120x120\" href=\"/apple-touch-icon-120x120.png\">");
        $out->addHeadItem( "icon-152", "<link rel=\"apple-touch-icon\" sizes=\"152x152\" href=\"/apple-touch-icon-152x152.png\">");
        $out->addHeadItem( "icon-180", "<link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"/apple-touch-icon-180x180.png\">");
        $out->addHeadItem( "mask-icon", "<link rel=\"mask-icon\" href=\"/safari-pinned-tab.svg?v=XBJgQp70gw\" color=\"#195905\">");
        $out->addHeadItem( "apple-mobile-web-app-title", "<meta name=\"apple-mobile-web-app-title\" content=\"WIKI\">");
        $out->addHeadItem( "fav-32", "<link rel=\"icon\" type=\"image/png\" sizes=\"32x32\" href=\"/favicon-32x32.png?v=XBJgQp70gw\">");
        $out->addHeadItem( "fav-16", "<link rel=\"icon\" type=\"image/png\" sizes=\"16x16\" href=\"/favicon-16x16.png?v=XBJgQp70gw\">");
        return true;
    }
}