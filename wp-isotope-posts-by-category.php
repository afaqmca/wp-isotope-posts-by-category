<?php
/**
 * @package isotope-posts-by-category
 * @version 1.0
 */
/*
  Plugin Name: WP Isotope Posts by Category
  Plugin URI: http://wordpress.org/plugins/isotope-posts-by-category/
  Description: This is Isotope based plugin. It work with bootstrap css.Short code [wp_isotope_posts]
  Author: Afaq Ahmad Khan
  Version: 1.0
  Author URI: http://crispedge.com/
 */
function my_scripts() {
    
    wp_enqueue_style( 'my-css-custom',  plugin_dir_url(__FILE__) . 'css/custom.css' );
    wp_enqueue_script('my-jquery-latest', plugin_dir_url(__FILE__) . 'js/jquery-latest.js', true, array('jquery'), '1.0');
    wp_enqueue_script('my-isotope', plugin_dir_url(__FILE__) . 'js/isotope.pkgd.js', true, array('jquery'), '1.0');
    wp_enqueue_script('my-isotope-custom', plugin_dir_url(__FILE__) . 'js/custom.js', true, array('jquery'), '1.0');
}

if (!is_admin()) {
    add_action('wp_enqueue_scripts', 'my_scripts', 20, 1);
}

function wp_isotope_get_filter_buttons($atts) {
    $tax='category';
    if(isset($atts['taxonomy']))
    {
        $tax=$atts['taxonomy'];
    }
    
    $terms = get_terms([
        'taxonomy' => $tax,
        'hide_empty' => true,
    ]);
    foreach ($terms as $key => $value) {
        ?>
        <a href="#" data-filter=".<?php echo $value->slug ?>"  class="btn btn-primary"><?php echo $value->name ?></a>
    <?php
    }
}

function wp_isotope_get_filter_posts($atts) {
    global $post;
    $tax='category';
    if(isset($atts['taxonomy']))
    {
        $tax=$atts['taxonomy'];
    }
    $posttype='post';
    if(isset($atts['posttype']))
    {
        $posttype=$atts['posttype'];
    }
    $col='column';
    $colNumber='4';
    if(isset($atts['columns']))
    {
        $colNumber=$atts['columns'];
    }
    
    $mtextCenter='mtext-center';
    $thumbnail='mthumbnail';
    if(isset($atts['bootstrap']))
    {
        $col='col-xs-12 col-sm-6 col-md-'.(12/(int)$colNumber);
        $mtextCenter='text-center';
        $thumbnail='thumbnail';
    }
    else {
        $col='column-'.$colNumber;
    }
    $args = array(
        'post_type' => $posttype,
        'posts_per_page' => -1
    );
    $loop = new WP_Query($args);
    while ($loop->have_posts()) : $loop->the_post();
        ?>
        <div class="<?php
        echo $col." ";
        $cats = wp_get_post_terms($post->ID, $tax, array("fields" => "all"));
        foreach ($cats as $value) {
            echo $value->slug . " ";
        }
        ?>">
            <div class="<?php echo $thumbnail;?>">
                <?php if (has_post_thumbnail()) : ?>
                    <img src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
        <?php endif; ?>
                <div class="caption">
                    <h3 class="<?php echo $mtextCenter;?>"><?php the_title(); ?></h3>
                    <p><?php the_excerpt(); ?></p>
                    <p class="text-center"><a href="<?php the_permalink(); ?>" class="btn btn-default" role="button">Read More</a></p>
                </div>
            </div>
        </div>


        <?php
    endwhile;
    wp_reset_postdata();
}

function wp_isotope_posts_func($atts) {
    global $content;
    ob_start();
    global $post;
    ?>
    <div class="container-fluid">
        <div class="portfolioFilter text-center">
            <a href="#" data-filter="*" class="current btn btn-primary">Alls Categories</a>
            <?php wp_isotope_get_filter_buttons($atts); ?>
        </div><br>
        <div class="row">
            <div class="portfolioContainer">
                <?php wp_isotope_get_filter_posts($atts); ?>
            </div>
        </div>
    </div>
    <?php
    $output = ob_get_clean();
    return $output;
}

if (!is_admin()) {
    add_shortcode('wp_isotope_posts', 'wp_isotope_posts_func');
}

