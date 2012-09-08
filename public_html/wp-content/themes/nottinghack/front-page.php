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
		
<?php
/* Nottinghack banner code
   We need to get last five posts tagged "homepage" and store details in array
   Later we will loop over this array twice!
   */

// look for ten, in case some don;t have images!
$bp = new WP_Query(array('posts_per_page' => 10, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'tag_slug__in' => array('homepage')));

$banner = array();

if ( $bp->have_posts() ) {
	while ( $bp->have_posts() ) {
		$bp->the_post();
		if (!has_post_thumbnail()) {
			continue;
		}
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "full" );
		$banner[] = array(
						  "title"	=>	get_the_title(),
						  "text"	=>	get_the_excerpt(),
						  "href"	=>	get_permalink(),
						  "image"	=>	$image[0],
						  );
		if (count($banner) == 5) {
			break;
		}
	}
}


?>
		
<div id="toprow">
	<div id="banner">
		<div id="banner_imgs">
<?php
		for ($i = 0; $i < count($banner); $i++) { ?>
			<a href="<?php echo($banner[$i]['href']); ?>"<?php if($i==0){ echo(' class="active"'); } ?>><img src="<?php echo($banner[$i]['image']); ?>" width="590" height="400" alt="Post Title" class="banner<?php echo($i+1); ?>" /><span><?php echo($banner[$i]['text']); ?></span></a>
<?php	} ?>
		</div><!-- banner_imgs -->
		<div id="banner_text">
			<?php echo($banner[0]['text']); ?>
		</div><!-- banner_text -->
		<div id="banner_nav">
			<div style="width: <?php echo(count($banner)*30); ?>px;">
<?php			for ($i = 0; $i < count($banner); $i++) { ?>
					<a href="#" class="<?php if($i==0){ echo('active '); } ?>banner<?php echo($i+1); ?>"></a>
<?php			} ?>
			</div>
		</div><!-- banner_nav -->
	</div><!-- banner -->
	
	<div id="news">
		<h2>Latest News</h2>
		<div id="highlight"></div>
		<ul>
<?php
		foreach ($banner as $item) { ?>
			<li><a href="<?php echo($item['href']); ?>"><?php echo($item['title']); ?></a></li>
<?php	} ?>
		</ul>
	</div><!-- news -->
</div><!-- toprow-->
		
		
		
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
	if ( is_active_sidebar( 'front-widget-area' ) ) : ?>
		<div class="front-widget-area" role="complementary">
			<ul class="xoxo">
		<?php
			if ( is_active_sidebar( 'front-widget-area' ) ) {
				dynamic_sidebar( 'front-widget-area' );
			}
		?>
			</ul>
		</div><!-- .widget-area -->
<?php endif; ?>
		
<?php get_footer(); ?>
