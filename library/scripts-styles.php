<?php

abstract class theme_dependency {
  static protected $dependency  = array();

  static protected $registered = array();

  abstract static public function add($name, array $settings);
  abstract static public function register();
  abstract static public function enqueue();
}

add_action('theme_admin_init', 'theme_scripts::admin_init');
class theme_scripts
  extends theme_dependency {

  const SOURCE = 1;
  const COMPRESS = 2;
  const COMBINE = 4;
  const COMPRESS_COMBINE = 6; // COMPRESS | COMBINE

  static protected $dependency = array(
    'jquery' => array(
      'name' => 'jquery',
      'src' => '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js',
      'dependency' => array(),
      'version', '1.8.3',
      'in_footer' => true,
    ),
    'theme-modernizr' => array(
      'name' => 'theme-modernizr',
      'path' => '/library/js/libs/modernizr.custom.min.js',
      'dependency' => array(),
      'version', '2.6.2',
      'in_footer' => false,
    ),
    'bootstrap-js' => array(
      'name' => 'bootstrap-js',
      'path' => '/library/bootstrap/js/bootstrap.min.js',
      'dependency' => array('jquery'),
      'version' => '2.2.2',
      'in_footer' => true,
    ),
    'theme-js' => array(
      'name' => 'theme-js',
      'path' => '/library/js/scripts.js',
      'compressed' => '/library/js/scripts.min.js',
      'version' => '',
      'in_footer' => true
    ),
  );

  static public function add($name, array $settings) {
    $_settings = $settings + array(
      'name' => $name,
      'src' => null,
      'path' => null,
      'compressed' => null,
      'dependency' => array(),
      'version' => '',
      'in_footer' => true,
    );
    if (
      isset($_settings['src'])
      || (
        isset($_settings['path'])
        && is_readable(get_stylesheet_directory().$_settings['path'])
      )
    ) {
      self::$dependency[$name] = $_settings;
    }
  }

  static public function register() {
    do_action('theme_scripts_register');
    foreach( self::$dependency as $name => $css ) {
      if ( isset(self::$dependency[$name]['registered']) && self::$dependency[$name]['registered'] ) {
        continue;
      }
      self::_do_register($name, $css);
    }
  }

  static public function enqueue() {
    foreach (self::$registered as $name) {
      wp_enqueue_script($name);
    }
  }

  static protected function _do_register($name, $js) {
    if (isset($js['src']) && $js['src']) {
      wp_register_script(
        $name,
        $js['src'],
        $js['dependency'],
        $js['version'],
        $js['in_footer']
      );
      return;
    }

    $_dirUri = get_stylesheet_directory_uri();
    $_dir = get_stylesheet_directory();
    $_file = $_dir.$js['path'];
    $_src = $_dirUri.$js['path'];
    if (
      (self::get_mode() & self::COMPRESS) // Compress AND/OR Combine
      && is_callable('exec')
      && isset($js['compressed'])
      && ($_compressed = $_dir.$js['compressed'])
      && is_file($_file)
      && is_readable($_file)
      // && filemtime($_lessFile) > filemtime($_file)
    ) {
      exec("uglifyjs -v {$_file} > {$_compressed}");
      $_src = $_dirUri.$js['compressed'];
    }
    wp_register_script(
      $name,
      $_src,
      $js['dependency'],
      $js['version'],
      $js['in_footer']
    );
    self::$registered[] = $name;
    self::$dependency[$name]['registered'] = true;
  }

  static public function get_mode() {
    static $mode;
    if ( isset($mode) ) {
      return $mode;
    }
    $options = get_option('theme_options');
    return isset($options[__CLASS__.'_mode']) ? intval($options[__CLASS__.'_mode']) : 0;
  }

  static public function admin_init($options) {
    $optionList = array(
      __CLASS__.'newblock' => array(
        'type' => 'newblock',
        'caption' => __('Script optimalization'),
      ),
      __CLASS__.'_mode' => array(
        'label' => __('Script optimalization mode','theme'),
        'type' => 'select',
        'value' => intval($options[__CLASS__.'_mode']) ? intval($options[__CLASS__.'_mode']) : self::get_mode(),
        'options' => array(
          self::SOURCE => __('Source files (uncompressed, not combined)'),
          self::COMPRESS => __('Compress files (not combined)'),
          //self::COMBINE => __('Combine files (uncompressed, combined)'),
          //self::COMPRESS_COMBINE => __('Compress and combine files'),
        ),
      ),
    );
    Theme_Admin::add_options($optionList);
  }
}

add_action('theme_admin_init', 'theme_styles::admin_init');
class theme_styles
  extends theme_dependency {

  const SOURCE = 1;
  const COMPRESS = 2;
  const COMBINE = 4;
  const COMPRESS_COMBINE = 6; // COMPRESS | COMBINE

  static protected $dependency = array(
    // register bootstrap CSS
    'bootstrap' => array(
      'name' => 'bootstrap',
      'less' => '/library/less/bootstrap/bootstrap.less',
      'path' => '/library/css/bootstrap.css',
      'compressed' => '/library/css/bootstrap.min.css',
      'dependency' => array(),
      'version' => '2.2.2',
      'media' => 'all'
    ),
    'bootstrap-responsive' => array(
      'name' => 'bootstrap-responsive',
      'less' => '/library/less/bootstrap/responsive.less',
      'path' => '/library/css/bootstrap-responsive.css',
      'compressed' => '/library/css/bootstrap-responsive.min.css',
      'dependency' => array(),
      'version' => '2.2.2',
      'media' => 'all'
    ),
    // register main stylesheet
    'theme-stylesheet' => array(
      'name' => 'theme-stylesheet',
      'less' => '/library/less/style.less',
      'path' => '/library/css/style.css',
      'compressed' => '/library/css/style.min.css',
      'dependency' => array(),
      'version' => '',
      'media' => 'all'
    ),
    'theme-ie-stylesheet' => array(
      'name '=> 'theme-ie-stylesheet',
      'less' => '/library/less/ie.less',
      'path' => '/library/css/ie.css',
      'compressed' => '/library/css/ie.min.css',
      'dependency' => array(),
      'version' => '',
      'media' => 'all'
    ),
  );

  static public function add($name, array $settings) {
    $_settings = $settings + array(
      'name' => $name,
      'src' => null,
      'path' => null,
      'less' => null,
      'compressed' => null,
      'dependency' => array(),
      'version' => '',
      'media' => 'all',
    );
    if (
      isset($_settings['src'])
      || (
        isset($_settings['path'])
        && is_readable(get_stylesheet_directory().$_settings['path'])
      )
    ) {
      self::$dependency[$name] = $_settings;
    }
  }

  static public function register() {
    do_action('theme_style_register');
    $_combine = self::get_mode() & self::COMBINE;

    foreach( self::$dependency as $name => $css ) {
      if ( isset(self::$dependency[$name]['registered']) && self::$dependency[$name]['registered'] ) {
        continue;
      }
      $_cssDir = get_stylesheet_directory();
      $_file = $_cssDir.$css['path'];
      if (
        is_callable('exec')
        && isset($css['less'])
        && ($_lessFile = $_cssDir.$css['less'])
        && is_file($_lessFile)
        && is_readable($_lessFile)
        // && filemtime($_lessFile) > filemtime($_file)
      ) {
        // Build CSS from LESS
        exec("lessc --line-numbers=comments {$_lessFile} > {$_file}");
        self::$dependency[$name]['#path'] = $_file;
        if (
          (self::get_mode() & self::COMPRESS)
          && isset($css['compressed'])
        ) {
          // Build compressed CSS from LESS
          $_fileCompressed = $_cssDir.$css['compressed'];
          exec("lessc --compress {$_lessFile} > {$_fileCompressed}");
          self::$dependency[$name]['#path_copmressed'] = $_fileCompressed;
        }
      }
      if ( $_combine ) {
        continue;
      }
      self::_do_register($name, $css);
    }
    if ( $_combine ) {
      self::_register_combined();
    }

  }

  static public function enqueue() {
    foreach( self::$registered as $name ) {
      wp_enqueue_style($name);
    }
  }

  static protected function _do_register($name, $css) {
    $_cssDirUri = get_stylesheet_directory_uri();
    $_cssDir = get_stylesheet_directory();
    $_file = $_cssDir.$css['path'];
    if (!is_readable($_file)) {
      //trigger_error('Path not readable: '.$_file, E_USER_WARNING);
      return false;
    }
    $_path = (self::get_mode() & self::COMPRESS)
      ? $_cssDirUri.$css['compressed']
      : $_cssDirUri.$css['path'];

    wp_register_style(
      $name,
      $_path,
      $css['dependency'],
      $css['version'],
      $css['media']
    );
    self::$registered[] = $name;
    return (self::$dependency[$name]['registered'] = true);
  }

  static protected function _register_combined() {
    $_cssDir = get_stylesheet_directory();
    $_cssDirUri = get_stylesheet_directory_uri();
    $_minified_file_path = '/library/css/_min_style.css';
    $_minified_file = $_cssDir.$_minified_file_path;
    $_minDir = dirname($_minified_file);
    if ( !$_minDir = self::_get_min_dir($_minDir) ) {
      return false;
    }

    $_last_mod = 0;
    if (
      $_has_minified = is_file($_minDir.'/style.css')
      && is_file($_minified_file)
    ) {
      $_last_mod = filemtime($_minified_file);
    }
    $_modified = true;//self::_source_modified_since($_last_mod);
    if ( !$_has_minified  || $_modified ) {
      $content = '';
      foreach( self::$dependency as $name => $css ) {
        $content .= file_get_contents(
          (self::get_mode() & self::COMPRESS)
            ? $css['#path_copmressed']
            : $css['#path']
        )."\n";
      }
      //$content = self::_compress_content($content);
      if ( false === file_put_contents($_minified_file, $content) ) {
        return false;
      }
    }

    wp_register_style(
      'style',
      $_cssDirUri.$_minified_file_path,
      array(),
      $_last_mod,
      'all'
    );
    self::$registered[] = 'style';
    return true;
  }

  static protected function _get_min_dir($_minDir) {
    if ( !is_dir($_minDir) ) {
      @mkdir($_minDir, 0777);
      @chmod($_minDir, 0777);
    }
    if (
      !is_dir($_minDir)
      || !is_writeable($_minDir)
    ) {
      return false;
    }
    return $_minDir;
  }

  static protected function _source_modified_since($since) {
    $_cssDir = get_stylesheet_directory();
    foreach( self::$dependency as $name => $css ) {
      if ( filemtime($_cssDir.$css['path']) > $since ) {
        return true;
      }
    }
    return false;
  }

  static public function get_mode() {
    static $mode;
    if ( isset($mode) ) {
      return $mode;
    }
    $options = get_option('theme_options');
    return isset($options[__CLASS__.'_mode']) ? intval($options[__CLASS__.'_mode']) : 0;
  }

  static public function admin_init($options) {
    $optionList = array(
      __CLASS__.'newblock' => array(
        'type' => 'newblock',
        'caption' => __('Stylesheet optimalization'),
      ),
      __CLASS__.'_mode' => array(
        'label' => __('CSS optimalization mode','theme'),
        'type' => 'select',
        'value' => intval($options[__CLASS__.'_mode']) ? intval($options[__CLASS__.'_mode']) : self::get_mode(),
        'options' => array(
          self::SOURCE => __('Source files (uncompressed, not combined)'),
          self::COMPRESS => __('Compress files (not combined)'),
          self::COMBINE => __('Combine files (uncompressed, combined)'),
          self::COMPRESS_COMBINE => __('Compress and combine files'),
        ),
      ),
    );
    Theme_Admin::add_options($optionList);
  }
}

//end
