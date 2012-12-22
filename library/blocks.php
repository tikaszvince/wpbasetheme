<?php


// Search Form
function theme_wpsearch($form) {
  return '
    <form role="search" method="get" id="searchform" action="'.home_url('/').'" >
      <label class="screen-reader-text" for="s">'.__('Search for:', 'theme').'</label>
      <input type="text" value="'.get_search_query().'" name="s" id="s" placeholder="'.esc_attr__('Search the Site...','theme').'" />
      <input type="submit" id="searchsubmit" value="'.esc_attr__('Search').'" />
    </form>'
  ;
}

function theme_error_no_post() {
  echo
    '<article id="post-not-found" class="hentry box clearfix">',
      '<header class="article-header">',
        '<h1 class="h2 headline">',__("Epic 404 - Article Not Found", "theme"),'</h1>',
      '</header> <!-- /article header -->',
      '<section class="entry-content">',
       '<p>',__("The article you were looking for was not found, but maybe try looking again!", "theme"),'</p>',
      '</section> <!-- /article section -->',
     '<section class="entry-content clearfix">',
      '<p>',get_search_form(false),'</p>',
      '</section> <!-- /search section -->',
    '</article> <!-- /article -->'
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
  if ( $numposts <= $posts_per_page ) { return; }
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
  echo $before.'<nav class="page-navigation"><ol class="page_navi clearfix">'."";
  if ($start_page >= 2 && $pages_to_show < $max_page) {
    $first_page_text = __("First", 'theme');
    echo '<li class="bpn-first-page-link"><a href="'.get_pagenum_link().'" title="'.$first_page_text.'">'.$first_page_text.'</a></li>';
  }
  echo '<li class="bpn-prev-link">';
  previous_posts_link('<<');
  echo '</li>';
  for($i = $start_page; $i  <= $end_page; $i++) {
    if($i == $paged) {
      echo '<li class="bpn-current">'.$i.'</li>';
    } else {
      echo '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
    }
  }
  echo '<li class="bpn-next-link">';
  next_posts_link('>>');
  echo '</li>';
  if ($end_page < $max_page) {
    $last_page_text = __( "Last", 'theme' );
    echo '<li class="bpn-last-page-link"><a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'">'.$last_page_text.'</a></li>';
  }
  echo '</ol></nav>'.$after."";
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

//end
