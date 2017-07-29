<?php
/**
 * @file
 * Contains bootstrap.php
 *
 * PHP Version 5
 */
spl_autoload_register(function($class) {

  $filename = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';

  if(!file_exists($filename)) {
    return false; // End autoloader function and skip to the next if available.
  }

  include $filename;
  return true; // End autoloader successfully.
});
