<?php

if (have_posts()) :
  while (have_posts()) :
    the_post();
    $_postClass = array('clearfix');
    $_postClass[] = has_post_thumbnail() ? 'has-thumb' : 'no-thumb';
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(join(' ',$_postClass)); ?> role="article" itemscope itemtype="http://schema.org/Article">
      <header class="article-header">
        <?php
        if ( is_single() ) {
          theme_post_thumb('theme-thumb-single-head', 'theme_post_thumb_fallback');
        }
        ?>
        <h1 class="h2" itemprop="name">
          <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" itemprop="url"><?php the_title(); ?></a>
        </h1>
        <p class="metas clearfix"><?php
          echo str_replace('<a ', '<a itemprop="author" ', theme_get_the_author_posts_link());
          printf(
            '<time class="meta updated" datetime="%1$s" itemprop="datePublished"><i></i>%2$s</time>'
            ,get_the_time('Y-m-d')
            ,get_the_time(get_option('date_format'))
          );
          if ( is_single() ) {
            if ( $categories = theme_get_category_list() ) {
              echo "\n",$categories;
            }
            if ( $_tagList = theme_get_tag_lists() ) {
              echo '<br/>',$_tagList,"\n";
            }
          }
        ?></p>
        <?php if ( is_single() ) : ?>
          <div class="fb-like" data-send="false" data-width="450" data-show-faces="false" data-font="arial"></div>
        <?php endif; ?>
      </header> <!-- end article header -->

      <section class="entry-content clearfix" itemprop="articleBody">
        <?php the_content(__('More','theme')); ?>
      </section> <!-- end article section -->
    </article> <!-- end article -->
  <?php
  endwhile;

  theme_page_navi();

else :
  theme_error_no_post();
endif;

//end

