<?php

function enqueue_function()
{
    // CSS Styling
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/vendor/bootstrap/css/bootstrap.min.css');
    wp_enqueue_style('font1', 'https://fonts.googleapis.com/css?family=Saira+Extra+Condensed:500,700');
    wp_enqueue_style('font2', 'https://fonts.googleapis.com/css?family=Muli:400,400i,800,800i');
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/vendor/fontawesome-free/css/all.min.css');
    wp_enqueue_style('resume', get_template_directory_uri() . '/css/resume.css');
    wp_enqueue_style('my-style', get_template_directory_uri() . '/css/resume.css');

    // JavaScript
    wp_enqueue_script('jquery', get_template_directory_uri() . 'vendor/jquery/jquery.min.js', array('jquery'), null, true);
    wp_enqueue_script('bootstrap-js', get_template_directory_uri() . '/vendor/bootstrap/js/bootstrap.bundle.min.js', array('jquery'), null, true);

    // Plugin JavaScript
    wp_enqueue_script('jquery-easing', get_template_directory_uri() . '/vendor/jquery-easing/jquery.easing.js', array('jquery'), null, true);

    // Custom scripts for this template
    wp_enqueue_script('resume-js', get_template_directory_uri() . '/js/resume.js', array('jquery'), null, true);
    wp_enqueue_script('myscript-js', get_template_directory_uri() . '/js/myscript.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'enqueue_function');


function register_resources()
{
    //register a menu
    register_nav_menu('main-menu', 'Main Menu');

    //register section
    $args = array(
        'public' => true,
        'label'  => 'Sections'
    );
    register_post_type('section', $args);

    //register section taxonomy
    $args = array(
        'label'        => 'Type',
        'public'       => true,
        'hierarchical' => true,
        'show_in_nav_menus' => true
    );
    register_taxonomy('type', 'section', $arg);

    //register experience
    $args = array(
        'public' => true,
        'label'  => 'Experience'
    );
    register_post_type('experience', $args);
}

add_action('init', 'register_resources');

function add_class_to_li($classes, $item, $args, $depth)
{
    $classes[] = 'nav-item';
    return $classes;
}
add_filter('nav_menu_css_class', 'add_class_to_li', 10, 4);


function add_class_to_anchors($atts)
{
    $atts['class'] = 'nav-link';
    return $atts;
}
add_filter('nav_menu_link_attributes', 'add_class_to_anchors', 10);

//short code

//[socialmedia]
function socialmedia_func($atts)
{

    ob_start();

    get_template_part('shortcode/socialmedia');

    return ob_get_clean();
}
add_shortcode('socialmedia', 'socialmedia_func');

//[section name=experience class=hi]
function section_func($atts)
{

    ob_start();
    // The Query
    $args = array(
        'post_type' => 'section',
        'name' => $atts['name'],
    );
    $the_query = new WP_Query($args);
    // The Loop
    while ($the_query->have_posts()) {
        $the_query->the_post();

        $suffix = 'default';

        //check section for type
        $types = get_the_terms(get_the_ID(), 'type');

        if ($types != false) {
            $type = $types[0];
            $slug = $type->slug;

            if (locate_template('partials/section/content-type-' . $slug . '.php')) {
                $suffix = 'type-' . $slug;
            }
        }
        //check section by section-slug
        $section_slug = get_post_field('post_name');
        if (locate_template('partials/section/content-' . $section_slug . '.php')) {
            $suffix = $section_slug;
        }
        get_template_part('partials/section/content', $suffix);
    }
    /* Restore original Post Data */
    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('section', 'section_func');
