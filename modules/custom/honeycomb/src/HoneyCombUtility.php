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
  public static function ajax_like_link( $action = 'like', $nid = 0, $title = '') {

    $path = 'base://honeycomb/image-like/' . $action . '/'  . $nid;

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
  public static function photo_like($uid, $fid, $tid = 0) {

    if ($fid) {

      $fields = array('uid' => $uid, 'fid' => $fid, 'tid' => $tid, 'created' => time());
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
  public static function photo_unlike($uid, $fid, $tid = 0) {
    if ($fid) {
      db_delete('honeycomb_like')
        ->condition('uid', $uid)
        ->condition('fid', $fid)
        ->condition('tid', $tid)
        ->execute();
    };
  }


}
