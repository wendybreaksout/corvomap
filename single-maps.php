<?php 

/* 
 * This is the default template for displaying a single map.
 * 
 * It can be overriden and customized by copying it to a theme directory
 * making the desired modifications.
 */
get_header(); 
?>

<?php get_template_part('includes/top_info'); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main" role="main">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
            <header class="entry-header">
			  <h1 class="single_map_title entry-title"><?php the_title(); ?></h1>
            </header>
		    <div class="entry-content">	
				<div id="map_images_container">
					<?php  
						$alternate_shortcode = get_post_meta($id, 'meta_map_alterate_image_shortcode', true);
						if ( ! empty( $alternate_shortcode )) {
							echo do_shortcode( $alternate_shortcode );
						}
						else {
							echo do_shortcode('[map_images]');
						}
					?>
				</div>
				
				<div id="map_desc_container">
					<?php  echo do_shortcode('[map_slogan]'); ?>
					<?php  echo do_shortcode('[map_desc]'); ?>
				</div>
				
				<div class="map_first_widget_area">
					<?php dynamic_sidebar('map_first_widget_area'); ?>
				</div>
				


				<div class="booking_form-wa cmp_one_third">
					<div class="map_second_widget_area">
						<?php dynamic_sidebar('map_second_widget_area') ;?>
					</div>
				</div>
								
				<div id="proposals_container">
					<?php  echo do_shortcode('[proposals]'); ?>
				</div>
				<div class="map_third_widget_area">
					<?php dynamic_sidebar('map_third_widget_area'); ?>
				</div> 
            </div> <!-- entry-content -->           
		</article> <!-- end .entry -->
	<?php endwhile; endif; ?>
   </main>
</div> <!-- end #content -->
<?php get_footer(); ?>
