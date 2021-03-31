<?php
/**
 * Timber theme class & other functions for Twig.
 *
 * @package Verbose_Doodle
 */

// Define paths to Twig templates
Timber::$dirname = array(
  'views/',
  'views/archive',
  'views/parts',
  'views/parts/comments',
  'views/single',
);

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;

/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class VerboseDoodle extends Timber\Site
{
  /** Add timber support */
  public function __construct()
  {
    add_action('after_setup_theme', array( $this, 'theme_supports' ));
    add_action('wp_enqueue_scripts', array( $this, 'verbose_doodle_enqueue_assets'));
    add_action('widgets_init', array( $this, 'verbose_doodle_custom_uikit_widgets_init'));
    add_filter('timber/context', array( $this, 'add_to_context' ));
    add_filter('timber/twig', array( $this, 'add_to_twig' ));
    add_action('init', array( $this, 'register_post_types' ));
    add_action('init', array( $this, 'register_taxonomies' ));
    add_action('init', array( $this, 'register_widget_areas' ));
    add_action('init', array( $this, 'register_navigation_menus' ));
    parent::__construct();
  }

  public function register_post_types()
  {
    // Register Post Types
  }

  public function register_taxonomies()
  {
    // Register Taxonomies
  }

  public function register_widget_areas()
  {
    // Register widget areas
    if (function_exists('register_sidebar')) {
      register_sidebar(array(
        'name' => esc_html__('Sidebar Area', 'verbose-doodle'),
        'id' => 'sidebar',
        'description' => esc_html__('Sidebar widget area; you can add multiple widgets here. Try the verbose doodle custom html widget with uikit markup.', 'verbose-doodle'),
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h3 class="uk-text-bold widget-title">',
        'after_title' => '</h3>'
      ));
      register_sidebar(array(
          'name' => esc_html__('Footer Area', 'verbose-doodle'),
          'id' => 'sidebar-footer',
          'description' => esc_html__('Footer widget area; use only one widget here. Try the verbose doodle custom html widget with uikit markup.', 'verbose-doodle'),
          'before_widget' => '',
          'after_widget' => '',
          'before_title' => '<h4 class="widget-title">',
          'after_title' => '</h4>'
      ));
    }
  }

  public function register_navigation_menus()
  {
    // This theme uses wp_nav_menu() in one locations.
    register_nav_menus(array(
      'main' => __('Main Menu', 'verbose-doodle'),
      'mobile' => __('Mobile Menu', 'verbose-doodle'),
    ));
  }

  public function add_to_context($context)
  {
    //add the site data to the context globally
    $context['site'] = $this;
    
    // optional args for Timber/Menu below. see options https://timber.github.io/docs/guides/menus/
    $main_menu_args = array(
      'depth' => 3,
    );
    // Initializing our menus
    $context['menu_main'] = new Timber\Menu('main');
    $context['menu_mobile'] = new Timber\Menu('mobile');
    // check for whether a menu exists
    $context['has_menu_main'] = has_nav_menu('main');
    $context['has_menu_mobile'] = has_nav_menu('mobile');

    // get the theme logo id
    $theme_logo_id = get_theme_mod('custom_logo');
    // get the theme logo url via the theme logo id
    $theme_logo_url = wp_get_attachment_image_url($theme_logo_id, 'full');
    // add theme logo url to the context
    $context['theme_logo_url'] = $theme_logo_url;

    // add sidebars to them context
    $context['sidebar_main']  = Timber::get_widgets('Sidebar Area');
    $context['sidebar_footer']   = Timber::get_widgets('Footer Area');

    return $context;
    
  }
  
  public function theme_supports()
  {
    // theme support for title tag
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');
    add_theme_support('post-formats', array(
      'gallery',
      'quote',
      'video',
      'aside',
      'image',
      'link'
    ));
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('woocommerce');
    // Switch default core markup for search form, comment form, and comments to output valid HTML5.
    add_theme_support('html5', array(
      'search-form',
      'comment-form',
      'comment-list',
      'gallery',
      'caption'
    ));
    // Add support for core custom logo.
    add_theme_support('custom-logo', array(
      'height' => 30,
      'width' => 261,
      'flex-width' => true,
      'flex-height' => true
    ));

    load_theme_textdomain( 'verbose-doodle', get_template_directory() . '/languages' );
    
    // remove emoji styles & scripts
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    
    // disbale embed functions & scripts (wp-embed-min.js). see theme-functions.php for additional
    // Remove the REST API endpoint.
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    // Turn off oEmbed auto discovery.
    add_filter( 'embed_oembed_discover', '__return_false' );
    // Don't filter oEmbed results.
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
    // Remove oEmbed discovery links.
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    add_filter( 'tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin' );
    // Remove all embeds rewrite rules.
    add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
    // Remove filter of the oEmbed result before any HTTP requests are made.
    remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
  }
  
  public function verbose_doodle_enqueue_assets()
  {
    
    wp_enqueue_style('verbose-doodle-base', get_template_directory_uri() . '/assets/css/base.css');
    wp_enqueue_style('verbose-doodle-global', get_template_directory_uri() . '/assets/css/global.css');
    
    wp_enqueue_script('verbose-doodle-base', get_template_directory_uri() . '/assets/js/global/base.min.js', '', '', false);
    wp_enqueue_script('verbose-doodle-global', get_template_directory_uri() . '/assets/js/global/global.min.js', '', '', true);
    
    wp_enqueue_script('verbose-doodle-theme', get_template_directory_uri() . '/assets/js/theme.js', '', '', true);
    
    // wp_enqueue_style('verbose-doodle-theme', get_stylesheet_uri());
  }
  
  public function verbose_doodle_custom_uikit_widgets_init()
  {
    register_widget("Verbose_Doodle_Custom_Widget_Class");
  }

  public function add_to_twig($twig)
  {
    /* this is where you can add your own functions to twig */
    $twig->addExtension(new Twig_Extension_StringLoader());
    return $twig;
  }
}

new VerboseDoodle();
