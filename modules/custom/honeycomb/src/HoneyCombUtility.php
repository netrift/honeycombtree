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
   * @param $action
   *  The like or unlike.
   */  
  public static function ajax_like_link( $action = 'like', $nid = 0, $tid = 0, $title = '') {

    $path = 'base://honeycomb/image-like/' . $action . '/'  . $nid . '/' . $tid;

    $options = array(
      'html' => TRUE,
      'attributes' => array(
      'class' => array('use-ajax'),
      )
    );
    $link = Url::fromUri($path, $options);

    return \Drupal::l($title, $link, array('html' => TRUE));
  }
  /**
   * @param $uid
   *  User ID
   * @param $fid
   *  File (Photo) ID
   * @param $tid
   *  Term id -- vendor category
   */ 
  public static function photo_like($uid, $nid, $tid = 0) {

    if ($nid && $uid > 0) {

      $fields = array('uid' => $uid, 'nid' => $nid, 'tid' => $tid, 'created' => time());
      db_insert('honeycomb_like')
        ->fields($fields)
        ->execute();

    }

  }

  /**
   * @param $uid
   *  User ID
   * @param $fid
   *  File (Photo) ID
   * @param $tid
   *  Term id -- vendor category
   */
  public static function photo_unlike($uid, $nid, $tid = 0) {

    if ($nid && $uid > 0) {
      db_delete('honeycomb_like')
        ->condition('uid', $uid)
        ->condition('nid', $nid)
        ->condition('tid', $tid)
        ->execute();
    };

  }


}
