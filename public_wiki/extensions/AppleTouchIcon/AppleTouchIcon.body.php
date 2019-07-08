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
        $out->addHeadItem( "icon", "<link rel=\"apple-touch-icon\" href=\"/apple-touch-icon.png\" />");
        // $out->addHeadItem( "icon-57", "<link rel=\"apple-touch-icon\" sizes=\"57x57\" href=\"/apple-touch-icon-57x57.png\" />");
        $out->addHeadItem( "icon-60", "<link rel=\"apple-touch-icon\" sizes=\"60x60\" href=\"/apple-touch-icon-60x60.png\" />");
        // $out->addHeadItem( "icon-72", "<link rel=\"apple-touch-icon\" sizes=\"72x72\" href=\"/apple-touch-icon-72x72.png\" />");
        $out->addHeadItem( "icon-76", "<link rel=\"apple-touch-icon\" sizes=\"76x76\" href=\"/apple-touch-icon-76x76.png\" />");
        // $out->addHeadItem( "icon-114", "<link rel=\"apple-touch-icon\" sizes=\"114x114\" href=\"/apple-touch-icon-114x114.png\" />");
        $out->addHeadItem( "icon-120", "<link rel=\"apple-touch-icon\" sizes=\"120x120\" href=\"/apple-touch-icon-120x120.png\" />");
        // $out->addHeadItem( "icon-144", "<link rel=\"apple-touch-icon\" sizes=\"144x144\" href=\"/apple-touch-icon-144x144.png\" />");
        $out->addHeadItem( "icon-152", "<link rel=\"apple-touch-icon\" sizes=\"152x152\" href=\"/apple-touch-icon-152x152.png\" />");
        $out->addHeadItem( "icon-180", "<link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"/apple-touch-icon-180x180.png\" />");
        return true;
    }
}