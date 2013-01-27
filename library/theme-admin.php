<?php

if ( file_exists(dirname(__FILE__).'/theme-additionals/theme-admin.php') ) {
  include_once dirname(__FILE__).'/theme-additionals/theme-admin.php';
}

class Theme_Admin {

  static public $inited = false;
  static protected $options = array();

  static public function init() {
    add_action('admin_menu', 'Theme_Admin::theme_add_admin_pages');
    $theme_options = get_option('theme_options');
    self::setup_default_options($theme_options);
    do_action('theme_admin_init', $theme_options);
  }

  static public function theme_add_admin_pages() {
    add_theme_page(
      __('Setup theme', 'theme'),
      __('Setup theme', 'theme'),
      8,
      'theme',
      'Theme_Admin::theme_options_page'
    );
    do_action('theme_admin_add_admin_pages');
  }

  static public function setup_default_options($options) {
    $optionList = array(
      'copyrightText' => array(
        'label' => __('Copyright', 'theme'),
        'type' => 'input',
        'value' => esc_attr($options['copyrightText']),
        'description' => sprintf(__('Shortcodes: %s', 'theme'), '<br/><ul><li>'.join('</li><li>',
          _theme_copyright_shortcode_help()
        )).'</li></ul>Current value:<br/>'.theme_get_copyright_text(),
      ),
    );
    self::add_options($optionList);
  }

  static public function add_options($options) {
    self::$options += $options;
  }

  static public function theme_options_page() {
    ini_set('display_errors',1);
    $themeName = get_template();
    $options = get_option('theme_options');
    $optionList = self::$options;
    ?>
  <div class="wrap"><h2><?php _e('Theme setup','theme') ?></h2>
    <?php
    if ( isset($_POST[ $themeName.'_update_me' ] ) ) {
      foreach ( $optionList as $name => $setup ) {
        if ( ! is_array( $setup ) ) {
          continue;
        }
        $value = is_array( $_POST[ $name ] )
          ? serialize( $_POST[ $name ] )
          : strip_tags( stripslashes( $_POST[ $name ] ) );
        $options[$name] = $value;
        $optionList[ $name ]['value'] = $value;
      }
      update_option('theme_options', $options);
      echo '<div class="updated"><p><strong>'. __('Options saved.').'</strong></p></div>';
    }
    ?>
    <form name="form1" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      <fieldset class="options">
        <input type="hidden" name="<?php echo $themeName; ?>_update_me" value="1" />
        <!--
        <p class="submit"><input type="submit" value="<?php _e('Update options');?> &raquo;" name="Submit" class="button-primary"/></p>
        -->
        <table class="form-table">
          <?php
          foreach ( $optionList as $name => $setup ) {
            if ( 'newblock' === $setup ) {
              echo '</table><table class="form-table">';
              continue;
            }
            elseif(
              $setup['type'] === 'newblock'
              && isset($setup['caption'])
            ) {
              echo '</table><h2>',$setup['caption'],'</h2><table class="form-table">';
              continue;
            }
            if (
              !isset( $setup['label'] )
              || !isset( $setup['value'] )
              || !in_array( $setup['type'], array( 'input', 'select', 'checkbox', 'rendered_html', 'callback' ) )
              || ('select' == $setup['type']&& count( (array)$setup['options'] ) <= 0)
              || ('callback' == $setup['type']&& (!isset($setup['callback']['callback']) || !is_callable($setup['callback']['callback'])))
            ) {
              var_dump($name, $setup);
              continue;
            }
            ?>
            <tr valign="top">
              <th scope="row"><?php echo $setup['label'] ?></th>
              <td><?php
                switch ( $setup[ 'type' ] ) {
                  case 'callback':
                    call_user_func($setup['callback']['callback'], $setup['callback']['params']);
                    break;

                  case 'rendered_html':
                    echo $setup['#html'];
                    break;

                  case 'select' :
                    echo '<select name="',$name,'" id="',$name,'">';
                    foreach ( $setup['options'] as $value => $label ) {
                      $selected = (isset( $setup['value'] ) && $setup['value'] == $value)
                        ? ' selected="selected"' : '';
                      echo '<option value="',$value,'" id="',$name,'_',$value,'"',$selected,'>',$label,'</option>';
                    }
                    echo '</select>';
                    break;

                  case 'checkbox' :
                    echo
                    '<input type="hidden" value="0" name="',$name,'" />',
                    '<input type="checkbox" name="',$name,'" id="',$name,'" value="1"',
                    ( $setup['value'] ? ' checked="checked"': '' ),'/>';
                    break;

                  case 'input':
                  default:
                    echo
                    '<input type="text" name="',$name,'" ',
                    'size="',( $setup['size'] ? $setup['size']: '40' ),'" ',
                    'id="',$name,'" value="',$setup['value'],'" />',
                    ( $setup['postfix'] ? $setup['postfix']: '' )
                    ;
                    break;
                }
                if ( isset( $setup['description'] ) ) : ?>
                  <br/><span class="setting-description"><?php echo $setup['description']; ?></span>
                  <?php endif; ?>
              </td>
            </tr>
            <?php } // /foreach ?>
        </table>
        <p class="submit"><input type="submit" value="<?php _e('Update options') ?> &raquo;" name="Submit" class="button-primary"/></p>
      </fieldset>
    </form>
  </div>
  <?php
  }
}

//end
