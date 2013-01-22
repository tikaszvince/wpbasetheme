<?php get_header(); global $wp_query;?>
  <h1 class="page-title"><?php
    the_post();
    if ( is_author() ) {
      printf(
        __('Author Archives: %s', 'theme'),
          sprintf(
            '<a href="%2$s">%1$s</a>'
            ,get_the_author()
            ,esc_url(get_author_posts_url(get_the_author_meta('ID')))
          )
      );
    }
    elseif ( is_tag() ) {
      printf(__('Posts Tagged: %s', 'theme'), single_tag_title('', false));
    }
    elseif ( is_day() ) {
      printf(__('Daily Archives: %s', 'theme'), get_the_time(__('l, F j, Y', 'theme')));
    }
    elseif ( is_month() ) {
      printf(__('Monthly Archives: %s', 'theme'), get_the_time(__('F Y', 'theme')));
    }
    elseif ( is_year() ) {
      printf(__('Yearly Archives: %s', 'theme'), get_the_time(__('Y','theme')));
    }
    rewind_posts();
  ?></h1>
  <div id="content"><div class="row-fluid">
    <div id="main" class="span8 first clearfix" role="main">
      <?php
      if ( is_author() ) {
        theme_author_bio(get_the_author_meta('ID'));
      }
      ?>
      <?php get_template_part('loop','index'); ?>
    </div> <!-- end #main -->
    <?php get_sidebar(); ?>
  </div></div> <!-- end #content -->
<?php get_footer(); ?>
