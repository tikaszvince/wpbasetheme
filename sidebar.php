
      <aside id="sidebar" class="sidebar span4 last clearfix" role="complementary">
        <?php if ( is_active_sidebar('sidebar') ) : ?>
          <?php dynamic_sidebar('sidebar'); ?>
        <?php else : ?>

          <!-- This content shows up if there are no widgets defined in the backend. -->
          <div class="alert help">
            <p><?php _e("Please activate some Widgets.", "theme");  ?></p>
          </div>
        <?php endif; ?>
      </aside>
