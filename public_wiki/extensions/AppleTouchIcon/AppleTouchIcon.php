<?php
/**
 * AppleTouchIcon extension
 * Add all the needed lines to head not just the one
 *
 * @file
 * @ingroup Extensions
 * @version 0.1
 * @author Matt Lloyd
 * @copyright © 2014 Matt Lloyd
 * @licence MIT
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define( 'APPLETOUCHICON_VERSION', '0.2, 2019-07-08' );

$wgAutoloadClasses['AppleTouchIconHooks'] = __DIR__ . '/AppleTouchIcon.body.php';
$wgHooks['BeforePageDisplay'][] = 'AppleTouchIconHooks::onBeforePageDisplay';

$wgExtensionCredits['other'][] = array(
	'path'           => __FILE__,
	'name'           => 'AppleTouchIcon',
	'author'         => array ( 'Matt Lloyd'),
	'descriptionmsg' => 'appletouchicon-desc',
	'url'            => 'https://wiki.nottinghack.org.uk/wiki/User:LWK',
	'version'        => APPLETOUCHICON_VERSION,
);

$wgExtensionMessagesFiles['AppleTouchIcon'] = dirname( __FILE__ ) . '/' . 'AppleTouchIcon.i18n.php';