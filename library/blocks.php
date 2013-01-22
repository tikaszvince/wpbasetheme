<?php


// Search Form
function theme_wpsearch($form) {
  static $count;
  if ( !isset($count) ) {
    $count = 0;
  }
  $_form = 'searchform';
  $_id = 's';
  $_btn = 'searchsubmit';
  if ( $count ) {
    $_form .= '-'.$count;
    $_id .= '-'.$count;
    $_btn .= '-'.$count;
  }
  $count++;
  return '
    <form role="search" method="get" class="searchform clearfix" id="'.$_form.'" action="'.home_url('/').'" >
      <label class="hidden" for="'.$_id.'">'.__('Search for:', 'theme').'</label>
      <input type="text" value="'.get_search_query().'" name="s" id="'.$_id.'" placeholder="'.esc_attr__('Search the Site...','theme').'" />
      <button type="submit" id="'.$_btn.'"><i></i>'.esc_attr__('Search').'</button>
    </form>'
  ;
}

function theme_error_no_post($show_head = true) {
  echo "\n",
    '<article id="post-not-found" class="hentry box clearfix">',"\n";
  if ( $show_head ) {
    echo
      '<header class="article-header">',
        '<h1 class="h2 headline">',__("Epic 404 - Article Not Found", "theme"),'</h1>',
      "</header><!-- /article header -->\n";
  }
  echo
      '<section class="entry-content">',
       '<p>',__("The article you were looking for was not found, but maybe try looking again!", "theme"),'</p>',
      "</section><!-- /article section -->\n",
      '<section class="entry-content clearfix">',
        get_search_form(false),
      "</section><!-- /search section -->\n",
    "</article><!-- /article -->\n\n"
  ;
}

function theme_archive_navi() {
  if (function_exists('page_navi')) { // if experimental feature is active
    page_navi(); // use the page navi function
  }
  else { // if it is disabled, display regular wp prev & next links
    $_prev = get_next_posts_link( '<i class="txt">'.__('&laquo; Older Entries', 'theme').'</i>' );
    $_next = get_previous_posts_link( '<i class="txt">'.__('Newer Entries &raquo;', 'theme').'</i>' );
    if ( !$_prev && !$_next ) {
      return;
    }
    if ( $_prev ) {
      $_prev = '<li class="prev-link">'.$_prev.'</li>';
    }
    if ( $_next ) {
      $_next = '<li class="next-link">'.$_next.'</li>';
    }
    echo
     '<nav class="wp-prev-next"><ul class="clearfix">',
        $_prev,
        $_next,
     '</ul></nav>'
    ;
  }
}

// Numeric Page Navi (built into the theme by default)
function theme_page_navi($before = '', $after = '') {
  global $wpdb, $wp_query;
  $request = $wp_query->request;
  $posts_per_page = intval(get_query_var('posts_per_page'));
  $paged = intval(get_query_var('paged'));
  $numposts = $wp_query->found_posts;
  $max_page = $wp_query->max_num_pages;

  if ( $numposts <= $posts_per_page ) {
    return;
  }

  if(empty($paged) || $paged == 0) {
    $paged = 1;
  }

  $pages_to_show = 7;
  $pages_to_show_minus_1 = $pages_to_show-1;
  $half_page_start = floor($pages_to_show_minus_1/2);
  $half_page_end = ceil($pages_to_show_minus_1/2);
  $start_page = $paged - $half_page_start;
  if($start_page <= 0) {
    $start_page = 1;
  }
  $end_page = $paged + $half_page_end;

  if(($end_page - $start_page) != $pages_to_show_minus_1) {
    $end_page = $start_page + $pages_to_show_minus_1;
  }
  if($end_page > $max_page) {
    $start_page = $max_page - $pages_to_show_minus_1;
    $end_page = $max_page;
  }
  if($start_page <= 0) {
    $start_page = 1;
  }

  echo $before.'<nav class="navigation"><ol class="links page-navi clearfix">'."";
  $first_page_text = __('&laquo; First','theme');
  $prev_page_text = __('&lsaquo; Previous','theme');
  $next_page_text = __('Next &rsaquo;','theme');
  $last_page_text = __('Last &raquo;','theme');

  // First link
  if ( $paged !== 1 ) {
    echo
      '<li class="first">',
         '<a href="',get_pagenum_link(),'" title="',$first_page_text,'">',$first_page_text,'</a>',
      '</li>';
  }

  // Previous link
  if ($prev = get_previous_posts_link($prev_page_text)) {
    echo '<li class="prev">',$prev,'</li>';
  }

  for($i = $start_page; $i  <= $end_page; $i++) {
    echo ($i == $paged)
      ? ('<li class="current"><span>'.$i.'</span></li>')
      : ('<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>');
  }

  // next link
  if ( $next = get_next_posts_link($next_page_text) ) {
    echo '<li class="next">',$next,'</li>';
  }

  // Last link
  if ( $max_page != $paged ) {
    echo
      '<li class="last">',
        '<a href="',get_pagenum_link($max_page),'" title="',$last_page_text,'">',$last_page_text,'</a>',
      '</li>';
  }

  echo '</ol></nav>'.$after;
}

// Related Posts Function (call using theme_related_posts(); )
function theme_related_posts() {
  echo '<ul id="related-posts">';
  global $post;
  $tags = wp_get_post_tags($post->ID);
  if($tags) {
    $tag_arr = '';
    foreach($tags as $tag) { $tag_arr .= $tag->slug . ','; }
    $args = array(
      'tag' => $tag_arr,
      'numberposts' => 5, /* you can change this to show more */
      'post__not_in' => array($post->ID)
    );
    $related_posts = get_posts($args);
    if($related_posts) {
      foreach ($related_posts as $post) {
        setup_postdata($post);
        $_link = get_permalink();
        $_title = the_title_attribute('echo=0');
        echo
        '<li class="related_post">'
        ,'<a class="entry-unrelated" href="',$_link,'" title="',$_title,'">'
        ,get_the_title()
        ,'</a>'
        ,'</li>';
      }
    }
    else {
      echo '<li class="no_related_post">',__('No Related Posts Yet!', 'theme'),'</li>';
    }
  }
  wp_reset_query();
  echo '</ul>';
}

function theme_post_thumb($default_size, $use_fallback = null) {
  echo get_theme_post_thumb($default_size, $use_fallback);
}

function get_theme_post_thumb($default_size, $use_fallback = null) {
  if ( !has_post_thumbnail() ) {
    $callback = 'get_'.$use_fallback;
    if ( is_callable($callback) ) {
      return $callback($default_size);
    }
    return '';
  }
  $mediaId = get_post_thumbnail_id();
  return get_theme_display_media_as_thumb($mediaId, $default_size);
}

function theme_display_media_as_thumb($mediaId, $default_size) {
  echo get_theme_display_media_as_thumb($mediaId, $default_size);
}

function get_theme_display_media_as_thumb($mediaId, $default_size, $post_id = null) {
  $_data = array();
  $_sizes = array();
  foreach( theme_get_image_sizes() as $name => $set ) {
    $_src = wp_get_attachment_image_src($mediaId, $name, true);
    $_data['data-src-'.$set['width']] = $_src[0];
    $_sizes[] = $set['width'];
  }
  $_data['data-sizes'] = join(',',$_sizes);
  return
    '<span class="thumb">'
    .get_the_post_thumbnail($post_id, $default_size, $_data)
    .'</span>';
}

function theme_author_bio($user_id = null) {
  echo get_theme_author_bio($user_id);
}

function get_theme_author_bio($user_id = null) {
  if ( !isset($user_id) ) {
    $user_id = get_the_author_meta('ID');
  }
  if ( !$user_id ) {
    return null;
  }
  $author = get_userdata($user_id);
  $_name = get_the_author_meta('display_name', $user_id);
  if(
    !function_exists('userphoto')
    || !userphoto_exists($user_id)
  ) {
    $_avatar = get_avatar($author, 200, '', $_name);
  }
  else {
    ob_start();
    userphoto($user_id);
    $_avatar = ob_get_clean();
  }

  return
    '<div class="author-bio"><div class="author clearfix">'.
      '<h4>'.$_name.'</h4>'.
      $_avatar.
      wpautop(get_the_author_meta('description', $user_id)).
    "</div></div><!-- /.author -->\n"
  ;
}

add_shortcode('author_bio', 'theme_shortcode_author_bio');
function theme_shortcode_author_bio($atts) {
  if (
    !isset($atts['author'])
    || !$atts['author']
  ) {
    return '';
  }
  if ( is_numeric($atts['author']) ) {
    $user = get_userdata($atts['author']);
  }
  elseif (is_scalar($atts['author'])) {
    $user = get_user_by('login', $atts['author']);
  }
  else {
    $user = null;
  }

  if ( !$user ) {
    return '';
  }
  return get_theme_author_bio($user->ID);
}


//end
