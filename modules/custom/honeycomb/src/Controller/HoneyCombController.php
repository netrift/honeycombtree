<?php
/**
 * @file
 * Contains \Drupal\honeycomb\Controller\HoneyCombController
 */

namespace Drupal\honeycomb\Controller;

use Drupal\Core\Session\AccountInterface;

/**
 * Main HoneyComb
 */
class HoneyCombController {



  /* 
   * List all Vendor categories 
   */
  public function vendor_categories() {
    return array(
      '#markup' => t('List all Vendor Categories here'),
    );
  }

  /* 
   * List all Vendor categories 
   * 
   * @param $uid
   *    The account user id
   */
  public function vendor_photos($uid) {
    return array(
      '#markup' => t('List all Vendor Photos'),
    );
  }
}
