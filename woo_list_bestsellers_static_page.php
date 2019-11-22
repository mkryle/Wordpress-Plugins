<?php 
/* Template Name: list 10 bestsellers l2*/
 ?>
<?php get_header(); ?>
	
  <?php
$args = array(
    'post_type' => 'product',
    'meta_key' => 'total_sales',
    'orderby' => 'meta_value_num',
    'posts_per_page' => 10,
);
$loop = new WP_Query( $args );

$top = 1;
echo '<h1>BESTSELLER TOP 10</h1><br><br>';
while ( $loop->have_posts() ) : $loop->the_post(); 
global $product; 

echo '<hr><h1>Number #'. $top.'</h1><hr>'; 
?>
<div>
<a href="<?php the_permalink(); ?>" id="id-<?php the_id(); ?>" title="<?php the_title(); ?>">

<?php if (has_post_thumbnail( $loop->post->ID )) 
        echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); 
        else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="product placeholder Image" width="65px" height="115px" />'; ?>
<h3><?php the_title(); $price?></h3>
<h4><?php the_content(); ?></h4>
<h1><?php
$regular_price = get_post_meta( get_the_ID(), '_regular_price', true);
if ($regular_price == ""){
$available_variations = $product->get_available_variations();
$variation_id=$available_variations[0]['variation_id']; 
$variable_product1= new WC_Product_Variation( $variation_id );
$regular_price = $variable_product1 ->regular_price;
}
echo 'pris: '.$regular_price.'kr';
?>
</h1>
</a><br><br><br>
</div>
<?php 
$top = $top + 1;
endwhile; ?>
<?php wp_reset_query(); ?>




<?php get_footer(); ?>




