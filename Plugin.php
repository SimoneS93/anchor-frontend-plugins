<?php

namespace plugins\frontend;

abstract class Plugin {
  protected static $plugins = array();
  
  /**
   * Load plugins from directory
   * @param string $dir
   */
  public static function load($dir) {
    $dir = dirname($dir) . DS;
    
    foreach (glob("{$dir}*/") as $plugin) {
      if (is_dir($plugin)) {
        $name = end(preg_split('/\//', $plugin, -1, PREG_SPLIT_NO_EMPTY));
        $filename = "{$plugin}{$name}.php";

        if (file_exists($filename)) {
          include $filename;
          $class = '\\plugins\\frontend\\' . ucfirst($name);
          
          if (class_exists($class)) {
            $o = new $class();
            $o instanceof Plugin and static::$plugins[] = $o;
          }
        }
      }
    }
  }

  /**
   * Run event on plugins
   * @param string $event
   */
  public static function run($event, array $arguments = array()) {
    $out = '';
    if (!empty(static::$plugins)) {
      $method = 'event' . ucfirst($event);
      
      
      foreach (static::$plugins as $plugin) {
        if (method_exists($plugin, $method))
          $out .= call_user_func_array(array($plugin, $method), $arguments);
      }
    }
    return $out;
  }
  
}

//autoload plugins
Plugin::load(__FILE__);
