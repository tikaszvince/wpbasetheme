<?php get_header(); ?>
  <h1 class="page-title"><?php the_title(); ?></h1>
  <div id="content"><div class="row-fluid">
    <div class="span12 first clearfix" role="main">
      <?php get_template_part('loop','index'); ?>
    </div> <!-- end #main -->
  </div></div> <!-- end #content -->

<?php get_footer(); ?>
