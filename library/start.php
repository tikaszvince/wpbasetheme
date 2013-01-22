<?php
add_action('after_setup_theme','theme_start', 15);

function theme_start() {
  add_editor_style();
  // launching operation cleanup
  add_action('init', 'theme_head_cleanup');
  // remove WP version from RSS
  add_filter('the_generator', 'theme_rss_version');
  // remove pesky injected css for recent comments widget
  add_filter('wp_head', 'theme_remove_wp_widget_recent_comments_style', 1);
  // clean up comment styles in the head
  add_action('wp_head', 'theme_remove_recent_comments_style', 1);
  // clean up gallery output in wp
  add_filter('gallery_style', 'theme_gallery_style');

  // enqueue base scripts and styles
  add_action('wp_enqueue_scripts', 'theme_scripts_and_styles', 999);
  // ie conditional wrapper
  add_filter('style_loader_tag', 'theme_ie_conditional', 10, 2);
  add_filter('style_loader_tag', 'theme_head_html_cleanup', 9, 2);

  // launching this stuff after theme setup
  add_action('after_setup_theme','theme_theme_support');
  // adding sidebars to Wordpress (these are created in functions.php)
  add_action('widgets_init', 'theme_register_sidebars');
  // adding the theme search form (created in functions.php)
  add_filter('get_search_form', 'theme_wpsearch');

  // cleaning up random code around images
  add_filter('the_content', 'theme_filter_ptags_on_images');
  // cleaning up excerpt
  add_filter('excerpt_more', 'theme_excerpt_more');
}

function theme_head_cleanup() {
  // category feeds
  // remove_action( 'wp_head', 'feed_links_extra', 3 );
  // post and comment feeds
  // remove_action( 'wp_head', 'feed_links', 2 );
  // EditURI link
  remove_action('wp_head', 'rsd_link');
  // windows live writer
  remove_action('wp_head', 'wlwmanifest_link');
  // index link
  remove_action('wp_head', 'index_rel_link');
  // previous link
  remove_action('wp_head', 'parent_post_rel_link', 10, 0);
  // start link
  remove_action('wp_head', 'start_post_rel_link', 10, 0);
  // links for adjacent posts
  remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
  // WP version
  remove_action('wp_head', 'wp_generator');
  // remove WP version from css
  add_filter('style_loader_src', 'theme_remove_wp_ver_css_js', 9999);
  // remove Wp version from scripts
  add_filter('script_loader_src', 'theme_remove_wp_ver_css_js', 9999);
}

// remove WP version from RSS
function theme_rss_version() { return ''; }

// remove WP version from scripts
function theme_remove_wp_ver_css_js($src) {
  if ( strpos($src, 'ver=') ) {
      $src = remove_query_arg('ver', $src);
  }
  return $src;
}

// remove injected CSS for recent comments widget
function theme_remove_wp_widget_recent_comments_style() {
   if ( has_filter('wp_head', 'wp_widget_recent_comments_style') ) {
      remove_filter('wp_head', 'wp_widget_recent_comments_style');
   }
}

// remove injected CSS from recent comments widget
function theme_remove_recent_comments_style() {
  global $wp_widget_factory;
  if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
    remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
  }
}

// remove injected CSS from gallery
function theme_gallery_style($css) {
  return preg_replace("!<style type='text/css'>(.*?)</style>!s", '', $css);
}

function theme_head_html_cleanup($tag, $handle) {
  return preg_replace(
    "/='([^']*?)'/",
    '="$1"',
    $tag
  );
}

// loading modernizr and jquery, and reply script
function theme_scripts_and_styles() {
  if (!is_admin()) {
    global $wp_scripts;
    wp_deregister_script('jquery');
    foreach($wp_scripts->registered as $handle => $script) {
      $wp_scripts->add_data($handle, 'group', 1);
    }

    wp_register_script('jquery','//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js',array(),'1.8.3',true);
    $_cssDirUri = get_stylesheet_directory_uri();
    // modernizr (without media query polyfill)
    wp_register_script('theme-modernizr', $_cssDirUri.'/library/js/libs/modernizr.custom.min.js', array(), '2.6.2', false);
    // Bootstrap js
    wp_register_script('bootstrap-js', $_cssDirUri.'/bootstrap/js/bootstrap.min.js', array('jquery'), '2.2.2', true);
    theme_styles::register();
    // ie-only style sheet
    wp_register_style('theme-ie-only', $_cssDirUri.'/library/css/ie.css', array(), '');
    // comment reply script for threaded comments
    if ( is_singular() && comments_open() && (get_option('thread_comments') == 1)) {
      wp_enqueue_script('comment-reply');
    }

    //adding scripts file in the footer
    wp_register_script('theme-js', $_cssDirUri.'/library/js/scripts.js', array('jquery'), '', true);

    // enqueue styles and scripts
    wp_enqueue_script('theme-modernizr');
    theme_styles::enqueue();
    wp_enqueue_style('theme-ie-only');

    /*
    I recommend using a plugin to call jQuery
    using the google cdn. That way it stays cached
    and your site will load faster.
    */
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap-js');
    wp_enqueue_script('theme-js');
  }
}

// adding the conditional wrapper around ie stylesheet
// source: http://code.garyjones.co.uk/ie-conditional-style-sheets-wordpress/
function theme_ie_conditional( $tag, $handle ) {
  if ( 'theme-ie-only' == $handle ) {
    $tag = "<!--[if lt IE 9]>\n".$tag."<![endif]-->\n";
  }
  return $tag;
}

// Adding WP 3+ Functions & Theme Support
function theme_theme_support() {
  // wp thumbnails (sizes handled in functions.php)
  add_theme_support('post-thumbnails');

  // default thumb size
  set_post_thumbnail_size(125, 125, true);

  // rss thingy
  add_theme_support('automatic-feed-links');

  // to add header image support go here: http://themble.com/support/adding-header-background-image-support/

  // adding post format support
  add_theme_support('post-formats', array(
    'aside', // title less blurb
    'gallery', // gallery of images
    'link', // quick link to other site
    'image', // an image
    'quote', // a quick quote
    'status', // a Facebook like status update
    'video', // video
    'audio', // audio
    'chat', // chat transcript
  ));

  // wp menus
  add_theme_support('menus');

  // registering wp3+ menus
  register_nav_menus(array(
    'main-nav' => __('The Main Menu', 'theme'), // main nav in header
    'footer-links' => __('Footer Links', 'theme') // secondary nav in footer
  ));
  if ( function_exists('theme_additional_theme_support') ) {
    theme_additional_theme_support();
  }
}

// the main menu
function theme_main_nav() {
  // display the wp3 menu if available
  wp_nav_menu(array(
    'container' => false, // remove nav container
    'container_class' => 'menu clearfix', // class of container (should you choose to use it)
    'menu' => __('The Main Menu', 'theme'), // nav name
    'menu_class' => 'nav top-nav clearfix', // adding custom nav class
    'theme_location' => 'main-nav', // where it's located in the theme
    'before' => '', // before the menu
    'after' => '', // after the menu
    'link_before' => '', // before each link
    'link_after' => '', // after each link
    'depth' => 0, // limit the depth of the nav
    'fallback_cb' => 'theme_main_nav_fallback', // fallback function
  ));
}

// the footer menu (should you choose to use one)
function theme_footer_links() {
  // display the wp3 menu if available
  wp_nav_menu(array(
    'container' => '', // remove nav container
    'container_class' => 'footer-links clearfix', // class of container (should you choose to use it)
    'menu' => __('Footer Links', 'theme'), // nav name
    'menu_class' => 'links footer-nav clearfix', // adding custom nav class
    'theme_location' => 'footer-links', // where it's located in the theme
    'before' => '', // before the menu
    'after' => '', // after the menu
    'link_before' => '', // before each link
    'link_after' => '', // after each link
    'depth' => 0, // limit the depth of the nav
    'fallback_cb' => 'theme_footer_links_fallback', // fallback function
  ));
}

// this is the fallback for header menu
function theme_main_nav_fallback() {
  wp_page_menu(array(
    'show_home' => true,
    'menu_class' => 'nav footer-nav clearfix', // adding custom nav class
    'include' => '',
    'exclude' => '',
    'echo' => true,
    'link_before' => '', // before each link
    'link_after' => '', // after each link
  ));
}

// this is the fallback for footer menu
function theme_footer_links_fallback() {
  /* you can put a default here if you like */
}

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function theme_filter_ptags_on_images($content){
   return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function theme_excerpt_more($more) {
  global $post;
  // edit here if you like
  return '...  <a href="'. get_permalink($post->ID) . '" title="Read '.get_the_title($post->ID).'">Read more &raquo;</a>';
}

// This is a modified the_author_posts_link() which just returns the link.
// This is necessary to allow usage of the usual l10n process with printf().
function theme_get_the_author_posts_link($author = null) {
  if ( !isset($author) ) {
    global $authordata;
    $author = $authordata;
  }
  if ( !is_object( $author ) ) {
    return false;
  }
  $link = sprintf(
    '<a href="%1$s" title="%2$s" rel="author" class="meta author"><i></i>%3$s</a>',
    get_author_posts_url( $author->ID, $author->user_nicename ),
    esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ), // No further l10n needed, core will take care of this one
    get_the_author()
  );
  return $link;
}

function theme_get_tag_lists($post_id = 0) {
  if ( $_tagList = get_the_tag_list('<i></i>', ', ', '', $post_id) ) {
    return
      '<span class="meta tags" title="'.__('Tags').'">'
        .$_tagList
      ."</span>";
  }
  return '';
}

function theme_get_category_list($post_id = 0) {
  if ( $categories = get_the_category($post_id) ) {
    $_cats = array();
    foreach ( $categories as $cat) {
      $_cats[] =
        '<a href="'.esc_url(get_category_link($cat->term_id)).'" '.
          'class="meta category" '.
          'title="'.esc_attr(sprintf(__("View all posts in %s"),$cat->name)).'" '.
          'rel="category"><i></i>'.$cat->name."</a>\n";
    }
    return join(', ',$_cats);
  }
  return '';
}

class theme_styles {

  static protected $css  = array(
    // register bootstrap CSS
    'bootstrap' => array(
      'name' => 'bootstrap',
      'path' => '/bootstrap/css/bootstrap.min.css',
      'dependency' => array(),
      'version' => '2.2.2',
      'media' => 'all'
    ),
    'bootstrap-responsive' => array(
      'name' => 'bootstrap-responsive',
      'path' => '/bootstrap/css/bootstrap-responsive.min.css',
      'dependency' => array(),
      'version' => '2.2.2',
      'media' => 'all'
    ),
    'theme-bootstrap' => array(
      'name' => 'theme-bootstrap',
      'path' => '/library/css/bootstrap-mods.css',
      'dependency' => array(),
      'version' => '',
      'media' => 'all'
    ),
  // register main stylesheet
    'theme-stylesheet' => array(
      'name' => 'theme-stylesheet',
      'path' => '/library/css/style.css',
      'dependency' => array(),
      'version' => '',
      'media' => 'all'
    ),
    'theme-responsive' => array(
      'name' => 'theme-responsive',
      'path' => '/library/css/responsive.css',
      'dependency' => array(),
      'version' => '',
      'media' => 'all'
    ),
  );

  static protected $registered = array();

  static public function add($name, $path, $dependency, $version, $media) {
    self::$css[$name] = array(
      'name' => $name,
      'path' => $path,
      'dependency' => $dependency,
      'version' => $version,
      'media' => $media
    );
    self::_do_register($name, self::$css[$name]);
  }

  static public function register() {
    $options = get_option('theme_options');
    if ( !$options['optimizeCss'] ) {
      self::_register_all();
    }
    else {
      self::_register_optimized();
    }
  }

  static public function enqueue() {
    foreach( self::$registered as $name ) {
      wp_enqueue_style($name);
    }
  }

  static protected function _register_all() {
    foreach( self::$css as $name => $css ) {
      if ( isset(self::$css[$name]['registered']) && self::$css[$name]['registered'] ) {
        continue;
      }
      self::_do_register($name, $css);
    }
  }

  static protected function _do_register($name, $css) {
    $_cssDirUri = get_stylesheet_directory_uri();
    wp_register_style(
      $name,
      $_cssDirUri.$css['path'],
      $css['dependency'],
      $css['version'],
      $css['media']
    );
    self::$registered[] = $name;
    self::$css[$name]['registered'] = true;
  }

  static protected function _register_optimized() {
    $_cssDir = get_stylesheet_directory();
    $_cssDirUri = get_stylesheet_directory_uri();
    $_minified_file_path = '/library/css/_min_style.css';
    $_minified_file = $_cssDir.$_minified_file_path;
    $_minDir = dirname($_minified_file);
    if ( !$_minDir = self::_get_min_dir($_minDir) ) {
      self::_register_all();
      return false;
    }

    $_has_minified = is_file($_minDir.'/style.css');
    $_last_mod = filemtime($_minified_file);
    $_modified = true;//self::_source_modified_since($_last_mod);
    if ( !$_has_minified  || $_modified ) {
      $content = '';
      foreach( self::$css as $name => $css ) {
        $content .= file_get_contents($_cssDir.$css['path'])."\n";
      }
      $content = self::_compress_content($content);
      if ( false === file_put_contents($_minified_file, $content) ) {
        self::_register_all();
        return false;
      }
    }

    wp_register_style(
      'style',
      $_cssDirUri.$_minified_file_path,
      array(),
      $_last_mod,
      'all'
    );
    self::$registered[] = 'style';
    return true;
  }

  static protected function _get_min_dir($_minDir) {
    if ( !is_dir($_minDir) ) {
      @mkdir($_minDir, 0777);
      @chmod($_minDir, 0777);
    }
    if (
      !is_dir($_minDir)
      || !is_writeable($_minDir)
    ) {
      return false;
    }
    return $_minDir;
  }

  static protected function _source_modified_since($since) {
    $_cssDir = get_stylesheet_directory();
    foreach( self::$css as $name => $css ) {
      if ( filemtime($_cssDir.$css['path']) > $since ) {
        return true;
      }
    }
    return false;
  }

  static protected function _compress_content($content) {
    // Remove comments
    $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
    // Remove space after colons
    $content = str_replace(': ', ':', $content);
    $content = str_replace(', ', ',', $content);
    // Remove whitespace
    $content = str_replace(array("\r\n", "\r", "\n", "\t"), '', $content);
    $content = str_replace("  ", '', $content);
    return $content;
  }
}

//end
