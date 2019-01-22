<?php
/**
 * Theme wrapper
 * @author     @retlehs
 * @link 	   http://roots.io
 * @editor     Themovation <themovation@gmail.com>
 * @version    1.0
 */
 
function roots_template_path() {
  return Roots_Wrapping::$main_template;
}

function themo_sidebar_path() {
  return new Roots_Wrapping('templates/sidebar.php');
}

class Roots_Wrapping {
  // Stores the full path to the main template file
  static $main_template;

  // Stores the base name of the template file; e.g. 'page' for 'page.php' etc.
  static $base;

  public function __construct($template = 'base.php') {
    $this->slug = basename($template, '.php');
    $this->templates = array($template);

    if (self::$base) {
      $str = substr($template, 0, -4);
      array_unshift($this->templates, sprintf($str . '-%s.php', self::$base));
    }
  }

  public function __toString() {
    $this->templates = apply_filters('roots_wrap_' . $this->slug, $this->templates);
    return locate_template($this->templates);
  }

  static function wrap($main) {

      // If Elementor Canvas Template, exit.
      if(isset($main) && basename($main) == 'canvas.php'){
          return $main;
      }


    self::$main_template = $main;
    self::$base = basename(self::$main_template, '.php');

    if (self::$base === 'index') {
      self::$base = false;
    }

    return new Roots_Wrapping();
  }
}
add_filter('template_include', array('Roots_Wrapping', 'wrap'), 99);
