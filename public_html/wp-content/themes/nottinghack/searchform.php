<?php
/**
 * Custom search bar for our theme
 *
 * @package WordPress
 * @subpackage Nottinghack
 * @since Nottinghack 1.0
 */
?>

<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
    <div><label class="screen-reader-text" for="s">Search for:</label>
        <input type="text" value="" name="s" id="s" /><input type="image" alt="Search" src="<?php bloginfo( 'template_url' ); ?>/images/search_button.jpg" align="top" id="searchbutton"/>
    </div>
</form>