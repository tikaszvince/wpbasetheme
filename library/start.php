<?php
add_action('after_setup_theme','theme_start', 15);

// Change theme/plugin url to static domain
add_filter('stylesheet_directory_uri', 'theme_stylesheet_directory_uri', 99, 3);
add_filter('wp_get_attachment_image_attributes', 'theme_wp_get_attachment_image_attributes', 10, 2);
add_filter('wp_get_attachment_url', 'theme_wp_get_attachment_url', 99, 2);

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

    theme_scripts::register();
    theme_styles::register();

    // comment reply script for threaded comments
    if ( is_singular() && comments_open() && (get_option('thread_comments') == 1)) {
      wp_enqueue_script('comment-reply');
    }

    theme_styles::enqueue();
    theme_scripts::enqueue();
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

function theme_stylesheet_directory_uri($stylesheet_dir_uri, $stylesheet, $theme_root_uri) {
  $options = get_option('theme_options');
  $_stUrl = rtrim($options['staticBaseUrlTheme'],'/');
  if (!isset($_stUrl) || !$_stUrl || $_stUrl == $theme_root_uri.'/'.$stylesheet) {
    return $stylesheet_dir_uri;
  }
  return $_stUrl;
}

function theme_wp_get_attachment_image_attributes($attr, $attachment) {
  /** @var $attachment WP_Post */
  $options = get_option('theme_options');
  $_stUrl = rtrim($options['staticBaseUrlFiles'],'/');
  if (isset($_stUrl) && $_stUrl) {
    $dir = dirname(get_post_meta($attachment->ID, '_wp_attached_file', true));
    $file = basename($attr['src']);
    $attr['src'] = $_stUrl .'/' .$dir .'/'.$file;
  }
  return $attr;
}

function theme_wp_get_attachment_url($url, $postId) {
  $options = get_option('theme_options');
  $_stUrl = rtrim($options['staticBaseUrlFiles'],'/');
  if (
    !isset($_stUrl)
    || !$_stUrl
    || !($post = get_post($postId))
    || $post->post_type !== 'attachment'
    || !(($uploads = wp_upload_dir()) && false === $uploads['error'])
  ) {
    return $url;
  }
  return str_replace($uploads['baseurl'], $_stUrl, $url);
}
//end
