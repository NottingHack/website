<?php
/**
 * The template for displaying the front page.
 *
 * @package WordPress
 * @subpackage Nottinghack
 * @since Nottinghack 1.0
 */

get_header(); ?>
		<div id="primary" class="widget-area" role="complementary">
			<ul class="xoxo">
				<li id="search" class="widget-container widget_search">
					<?php get_search_form(); ?>
				</li>
			</ul>
		</div>
		<div id="container">
			<div id="nh-carousel">
				<div id="nh-images">
					<div id="nh-slider">
						<?php nottinghack_carousel_images(); ?>
					</div>
					
					<div id="nh-ind">
						<?php nottinghack_carousel_ind(); ?>
					</div>
				</div>
				
				<div id="nh-text">
					<h2>Learn to Solder Workshop</h2>
					
					<div>
						<p>Let Nottinghack experts teach you how to solder, and make yourself a singing you can take home.</p>
					</div>
					
					<p class="cta"><a href="#">more details</a></p>
				</div>
			</div>
		</div><!-- #container -->
		<div id="content" role="main">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<div class="entry-content">
					<?php the_content(); ?>
				</div><!-- .entry-content -->
			</div><!-- #post-## -->
<?php endwhile; ?>
		</div><!-- #content -->

<?php
	if ( is_active_sidebar( 'front-top-side-widget-area' ) ) : ?>
		<div id="front-side-top" class="front-widget-area" role="complementary">
			<ul class="xoxo">
		<?php
			if ( is_active_sidebar( 'front-top-side-widget-area' ) ) {
				dynamic_sidebar( 'front-top-side-widget-area' );
			}
		?>
			</ul>
		</div><!-- #front-side-top .widget-area -->
<?php endif; ?>
<?php
	if ( is_active_sidebar( 'front-bottom-side-widget-area' ) ) : ?>

		<div id="front-side-bottom" class="front-widget-area" role="complementary">
			<ul class="xoxo">
				<?php dynamic_sidebar( 'front-bottom-side-widget-area' ); ?>
			</ul>
		</div><!-- #front-side-bottom .widget-area -->
		
<?php endif; ?>
		
<?php get_footer(); ?>
