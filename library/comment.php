<?php


// Comment Layout
function theme_comments($comment, $args, $depth) {
  $GLOBALS['comment'] = $comment; ?>
<li <?php comment_class(); ?>>
  <article id="comment-<?php comment_ID(); ?>" class="clearfix">
    <header class="comment-author vcard">
      <?php
      /*
      this is the new responsive optimized comment image. It used the new HTML5 data-attribute
      to display comment gravatars on larger screens only. What this means is that on
      larger posts, mobile sites don't have a ton of requests for comment images.
      This makes load time incredibly fast! If you'd like to change it back,
      just replace it with the regular wordpress gravatar call:
      echo get_avatar($comment,$size='32',$default='<path_to_url>' );
      */
      ?>
      <!-- custom gravatar call -->
      <?php
      // create variable
      $bgauthemail = get_comment_author_email();
      ?>
      <img data-gravatar="http://www.gravatar.com/avatar/<?php echo md5($bgauthemail); ?>?s=32" class="load-gravatar avatar avatar-48 photo" height="32" width="32" src="<?php echo get_template_directory_uri(); ?>/library/img/nothing.gif" />
      <!-- end custom gravatar call -->
      <?php printf('<cite class="fn">%s</cite>', get_comment_author_link()) ?>
      <time datetime="<?php echo comment_time('Y-m-d'); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_time(__('l, F j, Y', 'theme')); ?> </a></time>
      <?php edit_comment_link(__('(Edit)', 'theme'),'  ','') ?>
    </header>
    <?php if ($comment->comment_approved == '0') : ?>
    <div class="alert info">
      <p><?php _e('Your comment is awaiting moderation.', 'theme') ?></p>
    </div>
    <?php endif; ?>
    <section class="comment_content clearfix">
      <?php comment_text() ?>
    </section>
    <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
  </article>
  <!-- </li> is added by WordPress automatically -->
<?php
} // don't remove this bracket!

//end
