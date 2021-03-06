
  <footer class="footer page-footer" role="contentinfo">
    <?php if ( is_active_sidebar('footerwidgets') ) : ?>
      <aside class="footer-widgets row-fluid">
        <?php dynamic_sidebar( 'footerwidgets' ); ?>
      </aside>
    <?php endif; ?>

    <section class="page-closer clearfix">
      <nav role="navigation">
        <?php theme_footer_links(); ?>
      </nav>
      <div class="source-org copyright">
        <?php theme_copyright_text() ?>
      </div>

      <div class="fingerprint clearfix">
        <?php fingerprint_4image() ?>
      </div>
    </section>
  </footer> <!-- end footer -->

</div> <!-- end #container -->

<!-- all js scripts are loaded in library/start.php -->
<?php wp_footer(); ?>

</body>
</html> <!-- end page. what a ride! -->
