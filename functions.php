<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
	$timber = new Timber\Timber();
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Timber\Site {
	/** Add timber support. */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		parent::__construct();

		// Enqueue assets
		function stpeter_enqueue_assets() {
			// Site scripts
			wp_enqueue_script( 'js', get_template_directory_uri() . '/dist/main.js', array(), '1.0.0', true );			
		
		}
		add_action('wp_enqueue_scripts', 'stpeter_enqueue_assets');

    // Custom image sizes
    add_image_size( '1290x1140', 1290, 1140, true );
    add_image_size( '645x570', 645, 570, true );
    add_image_size( '700x740', 700, 740, true );  
    add_image_size( '350x370', 350, 370, true );        
    add_image_size( '2000w', 2000 );
    
    // Exercpt for Pages
    add_post_type_support( 'page', 'excerpt' );

    // Options page 
    if( function_exists('acf_add_options_page') ) {
      
      acf_add_options_page(

        array(
          'page_title' => 'Options',
          'menu_title' => 'Options',
          'menu_slug' => 'options',
          'capability' => 'edit_posts',
          'redirect'		=> false
        )
      );	
    }
	}
	/** This is where you can register custom post types. */
	public function register_post_types() {

	}
	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {

	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['foo']   = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::context();';
		$context['site']  = $this;
    $context['primary_menu'] = new Timber\Menu('Primary Navigation');
    $context['footer_menu'] = new Timber\Menu('Footer Navigation');
    $context['options'] = get_fields('option');
		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		add_theme_support( 'menus' );
	}

	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
		$twig->addFilter( new Twig\TwigFilter( 'myfoo', array( $this, 'myfoo' ) ) );
		return $twig;
	}

}

new StarterSite();

// add_filter( 'timber_context', 'mytheme_timber_context'  );

// function mytheme_timber_context( $context ) {
//     $context['options'] = get_fields('option');
//     return $context;
// }

//Custom Navigation Menu
// function wpb_custom_new_menu() {
//   register_nav_menus(
//     array(
//       'main-navigation' => __( 'Main Navigation' ),
//       'footer-navigation' => __( 'Footer Navigation' )
//     )
//   );
// }
// add_action( 'init', 'wpb_custom_new_menu' );

// add_filter( 'timber/context', 'add_to_context' );


// function add_to_context( $context ) {
// 	$context['main-navigation']  = new Timber\Menu('Main Navigation');
// 	$context['footer-navigation'] = new Timber\Menu('Footer Navigation');
// 	// ...
// 	return $context;
// }