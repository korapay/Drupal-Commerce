<?php

/**
 * @file
 * Provides functionality for commerce_korapay_gateway.
 */


 
/**
 * Implements hook_help().
 */
function commerce_korapay_gateway_help($route_name, \Drupal\Core\Routing\RouteMatchInterface $route_match) {
  if ($route_name == 'help.page.commerce_korapay_gateway') {
    $text = file_get_contents(dirname(__FILE__) . '/README.md');
    // Return a line-break version of the module README.md file.
    return '<pre>' . $text . '</pre>';
  }
}
