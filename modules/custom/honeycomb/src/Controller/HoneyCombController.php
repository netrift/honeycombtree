<?php
/**
 * @file
 * Contains \Drupal\honeycomb\Controller\HoneyCombController
 */

namespace Drupal\honeycomb\Controller;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;

/**
 * Main HoneyComb
 */
class HoneyCombController {


  /*
   * Default Vendor Home page.
   */
  public function home() {
    return array(
      '#markup' => t('Default Homage page. List all the Vendor Images Masonary format here.'),
    );
  }

  /*
   * Default Vendor Home page.
   */
  public function dashboard() {
    $user = \Drupal::currentUser();
    $userName = $user->getUsername();

    //$roles = $user->getRoles(TRUE); // don't get defualt locked roles

    // Generate the add link . First heck to make sure that user
    // doesn't already have a company added.
    $add_url = Url::fromRoute('node.add', array('node_type' => 'company'));
    $add_url = \Drupal::l('Add New Company', $add_url);

    if ($user->hasPermission('create company content') ) {
      $output = t('Welcome @name. You have permission to ceate a company. !add_company', array('@name' => $userName, '!add_company' => $add_url));
    }
    else {
      $output = 'Welcome ' . $userName;

    };

    return array(
      '#markup' => $output,
    );
  }


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
