<?php get_header(); ?>
  <div id="content"><div class="row-fluid">
    <div id="main" class="span8 first clearfix" role="main">
      <?php get_template_part('loop','index'); ?>
      <?php
      $_prev = get_previous_post();
      $_next = get_next_post();
      if ( $_prev || $_next ) {
        echo '<nav class="navigation"><ul class="links page-navi clearfix">';
        if ( $_next ) {
          echo '<li class="next-post">';
          next_post_link('&laquo; %link', __('Previous post', 'theme'));
          echo '</li>';
        }
        if ( $_prev ) {
          echo '<li class="previous-post">';
          previous_post_link('%link &raquo;', __('Next post', 'theme'));
          echo '</li>';
        }
        echo '</ul></nav>';
      }
      ?>
    </div> <!-- end #main -->
    <?php get_sidebar(); ?>
  </div></div> <!-- end #content -->
<?php get_footer(); ?>
