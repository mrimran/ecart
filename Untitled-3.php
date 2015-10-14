<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package _tk
 */
?>

   <script>
		jQuery(document).ready(function() {
			onPageCall();
		});
		jQuery(window).resize(function() {
			onPageCall();
		});
</script>
				</div>
			</div><!-- close .main-content -->
<div class="home-dropup-right">
		<div class="home-du-detials custom-scroll-news" style="display: block;">

		<?php
		$args = array(
                'post_type'=> 'news-and-events',
                'order' => 'ASC',
                'post_status' => 'publish',
                'posts_per_page' => 5,

              );
        $query = new WP_Query( $args );
		//query_posts('post_type=news-and-events&order=ASC&post_status=publish&posts_per_page=-1');
        if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
        $query->the_post();
        ?>
    	<a href="<?php the_permalink(); ?>">
        <h3> <?php  the_title(); ?>  </h3>
        </a>
            <div class="newsContentsHome">
            	<?php //echo '<p>' . wp_trim_words( get_the_content(), 40 ) . '</p>'; ?>
                <?php echo   get_the_excerpt() ; ?>
            </div>
		<?php
        }
        }
        wp_reset_query();
        ?>
		</div>
		<div class="home-du-trigger">
		<div>
			<h3>NEWS & EVENTS</h3>
			<i class="fa fa-angle-up"></i>
            <p>Read about our latest news and events.</p>
		</div>
	</div>
</div>
			<footer id="colophon" class="site-footer animated" role="contentinfo">
				<div class="container">
					<div class="row">
						<div class="site-footer-inner">
							<div class="site-info">
								<div class="col-sm-6 f-div">
									<div class="footer-menu"><?php wp_nav_menu( array('menu' => 'footer-menu' )); ?></div>
								</div>
								<div class="col-sm-1 f-div">
									<div class="social-menu"><?php wp_nav_menu( array('menu' => 'social-links' )); ?></div>
								</div>
								<div class="col-sm-5 f-div text-right">
									<p class="copyrights"> &copy; <?php echo date('Y') ?> Palma Holding, All Rights Reserved.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php /*?><div id='new_text_ticker'>
				<div class="news__text_ticker">
					<?php
						// print_r(wp_get_single_post( 1810)) ;
						$post1=wp_get_single_post( 1810);
						$post2=wp_get_single_post( 1812);
					?>
					<ul>
						<li>
							<?php
							echo "<strong>".$post1->post_title."</strong> ".$post1->post_content;
							?>
						</li>
						<li>
							<?php
							echo "<strong>".$post2->post_title."</strong> ".$post2->post_content;
							?>
						</li>
					</ul>
				</div>
			</div><?php */?>
			</footer>
		</div>
	</div>
	</div>
	<?php wp_footer(); ?>

</body>
</html>