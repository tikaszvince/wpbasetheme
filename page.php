<?php get_header(); ?>
  <div id="content">
    <div id="inner-content" class="wrap clearfix">
      <div id="main" class="eightcol first clearfix" role="main">
        <?php if (have_posts()) : ?>
          <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
              <header class="article-header">
                <h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
                <p class="byline vcard"><?php
                  printf(
                    __('Posted <time class="updated" datetime="%1$s" pubdate>%2$s</time> by <span class="author">%3$s</span>.', 'theme')
                    ,get_the_time('Y-m-j')
                    ,get_the_time(__('F jS, Y', 'theme'))
                    ,theme_get_the_author_posts_link()
                  );
                ?></p>
              </header> <!-- end article header -->

              <section class="entry-content clearfix" itemprop="articleBody">
                <?php the_content(); ?>
              </section> <!-- end article section -->

              <footer class="article-footer">
                <?php the_tags('<span class="tags">' . __('Tags:', 'theme') . '</span> ', ', ', ''); ?>
              </footer> <!-- end article footer -->

              <?php comments_template(); ?>
            </article> <!-- end article -->
          <?php endwhile; ?>
        <?php else : ?>
          <?php theme_error_no_post(); ?>
        <?php endif; ?>
      </div> <!-- end #main -->

      <?php get_sidebar(); ?>
    </div> <!-- end #inner-content -->
  </div> <!-- end #content -->

<?php get_footer(); ?>