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

    // Disable Gutenberg
    add_filter('use_block_editor_for_post', '__return_false', 10);
    function cw_deregister_styles() { wp_dequeue_style( 'wp-block-library' );}
    add_action( 'wp_print_styles', 'cw_deregister_styles', 100 );    

    // Enqueue assets
		function cw_enqueue_assets() {

			// Swiper
			wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css' );
			wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js', array (), 9.0, true);	

			// Fonts
			// wp_enqueue_style(
			// 	'fonts',
			// 	get_template_directory_uri() . '/static/fonts/fonts.css',
			// 	false,
			// 	filemtime(get_template_directory() . '/static/fonts/fonts.css')
			// );
      
			// Site styles
			wp_enqueue_style(
				'styles',
				get_template_directory_uri() . '/dist/style.css',
				false,
				filemtime(get_template_directory() . '/dist/style.css')
			);

			// Site scripts
			wp_enqueue_script( 'js', get_template_directory_uri() . '/static/site.js', array(), '1.0.0', true );			
		
		}
		add_action('wp_enqueue_scripts', 'cw_enqueue_assets');

    // ACF Options
    if( function_exists('acf_add_options_page') ) {
      acf_add_options_page();
    }

    // Custom imges sizes
		// add_image_size( '540x310', 540, 310, true );
    // add_image_size( 'w1220', 1220 );

    // Custom ACF toolbars
    // https://www.advancedcustomfields.com/resources/customize-the-wysiwyg-toolbars
    // ------------------------------------------------------------------
    add_filter("acf/fields/wysiwyg/toolbars", "my_toolbars");
    function my_toolbars($toolbars)
    {
      $toolbars["Very Simple"] = [];
      $toolbars["Very Simple"][1] = ["bold", "italic", "link", "bullist", "numlist"];
      return $toolbars;
    }	  

    // Exercpt for Pages
    add_post_type_support( 'page', 'excerpt' ); 
    
		// Move Yoast to bottom
		function yoasttobottom() {
			return 'low';
		}
		add_filter( 'wpseo_metabox_prio', 'yoasttobottom');    

		parent::__construct();
    
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
		// $context['foo']   = 'bar';
		// $context['stuff'] = 'I am a value set in your functions.php file';
		// $context['notes'] = 'These values are available everytime you call Timber::context();';
		$context['site']  = $this;
    $context['menu'] = new Timber\Menu('Primary Navigation');
    $context['options'] = get_fields('option');
    
		// Loop for Offices
		// $office_args = array(
		// 	'post_type' => 'office',
    //   'orderby' => 'menu_order',
    //   'order' => 'ASC'
		// );
		// $context['offices_loop'] = Timber::get_posts($office_args);	

		// Loop for Posts
		// $latest_post_args = array(
		// 	'post_type' => 'post',
		// 	'posts_per_page' => 6,
		// );
		// $context['latest_posts_loop'] = Timber::get_posts($latest_post_args);	

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
	// public function myfoo( $text ) {
	// 	$text .= ' bar!';
	// 	return $text;
	// }

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
		// $twig->addFilter( new Twig\TwigFilter( 'myfoo', array( $this, 'myfoo' ) ) );
		return $twig;
	}

}

new StarterSite();