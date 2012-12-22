<?php get_header(); ?>
  <div id="content">
    <div id="inner-content" class="wrap clearfix">
      <div id="main" class="eightcol first clearfix" role="main">
        <?php if (have_posts()) : ?>
          <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">
              <header class="article-header">
                <h1 class="h2">
                  <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                </h1>
                <p class="byline vcard"><?php
                  printf(
                    __('Posted <time class="updated" datetime="%1$s" pubdate>%2$s</time> by <span class="author">%3$s</span> <span class="amp">&</span> filed under %4$s.', 'theme')
                    ,get_the_time('Y-m-j')
                    ,get_the_time(get_option('date_format'))
                    ,theme_get_the_author_posts_link()
                    ,get_the_category_list(', ')
                  );
                ?></p>
              </header> <!-- end article header -->

              <section class="entry-content clearfix">
                <?php the_content(); ?>
              </section> <!-- end article section -->

              <footer class="article-footer">
                <p class="tags"><?php the_tags('<span class="tags-title">' . __('Tags:', 'theme') . '</span> ', ', ', ''); ?></p>
              </footer> <!-- end article footer -->

              <?php // comments_template(); // uncomment if you want to use them ?>
            </article> <!-- end article -->
          <?php endwhile; ?>
          <?php theme_archive_navi() ?>

        <?php else : ?>
          <?php theme_error_no_post(); ?>
        <?php endif; ?>
      </div> <!-- end #main -->

      <?php get_sidebar(); ?>

    </div> <!-- end #inner-content -->
  </div> <!-- end #content -->

<?php get_footer(); ?>
