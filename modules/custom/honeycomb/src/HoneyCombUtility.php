<?php

/**
 * @file
 * Contains \Drupal\honeycomb\HoneyCombUtility. - utility functions. 
 */

namespace Drupal\honeycomb;

use Drupal\Core\Url;


/**
 * Defines a class to provide utility functions. 
 */
class HoneyCombUtility {

  /**
   * Output the ajax like/love link. 
   */  
  public static function ajax_like_link( $action = 'like', $nid = 0, $title = '') {

    $path = 'base://honeycomb/image-like/' . $action . '/'  . $nid;

    $options = array(
      'html' => TRUE,
      'attributes' => array(
      'class' => array('use-ajax'),
      )
    );
    $link = Url::fromUri($path, $options);

    return $link;
  }

}
