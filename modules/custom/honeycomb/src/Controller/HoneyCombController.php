<?php
/**
 * @file
 * Contains \Drupal\honeycomb\Controller\HoneyCombController
 */

namespace Drupal\honeycomb\Controller;

use Drupal\honeycomb\HoneyCombUtility;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Main HoneyComb
 */
class HoneyCombController {


  /*
   * Default Vendor Home page.
   */
  public function vendor_photos($uid) {
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

    // Generate the add link . First check to make sure that user
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
   * Default Home Page 
   * 
   */
  public function home() {

    $query = db_select('node_field_data', 'n');
    $query->leftJoin('file_usage', 'fu', 'n.nid = fu.id');
    $query->leftJoin('file_managed', 'fm', 'fu.fid = fm.fid');

    $query->fields('fm', array('uri'));
    $query->fields('n', array('nid'));

    $query->condition('n.status', 1);
    $query->condition('n.type', 'vendor_image');

    $query->orderRandom();
    $result = $query->execute();

    $output = $images = '';

    foreach ($result as $record) {
      $image_url = ImageStyle::load('image_masonry')->buildUrl($record->uri);
      $image = '<img src="' . $image_url  . '">';
      $options = array(
        'html' => TRUE,
        'attributes' => array(
          'class' => array('colorbox'),
        )
      );
      $path = file_create_url($record->uri);
      $full_url = Url::fromUri($path, $options);
      $image_link = \Drupal::l($image, $full_url, array('html' => TRUE));

      if (empty($_SESSION['like-images'][$record->nid])) {
        $like_link = HoneyCombUtility::ajax_like_link('like', $record->nid, 'Like');
      }
      else {
        $like_link = HoneyCombUtility::ajax_like_link('unlike', $record->nid, 'UnLike');
      };

      $images .= '
      <div class="selection-photo">
        ' . $image_link . '
        <div id="like-action-' . $record->nid . '" class="like-wrapper">
          ' . $like_link . '
        </div>
      </div>
      ';
    };
    $output = '
    <div id="photo-display-container">
        ' . $images . '
    </div>
    ';


    return array(
      '#markup' => $output ,
      '#attached' => array (
        'library' => array (
          'honeycomb/honeycomb.image-selection',
        ),
      ),
    );

  }

  /**
   * Like Action Ajax CallBack
   */
  public function like_action($action = 'like' , $nid = 0 , $title = '') {
    $account = \Drupal::currentUser();

    if ($action == 'like') {
        $like_link = HoneyCombUtility::ajax_like_link('unlike', $nid, 'UnLike');
        $_SESSION['like-images'][$nid] = $nid;
        //HoneyCombUtility::photo_like($account->id(), $nid, $tid);
    }
    else {
        $like_link = HoneyCombUtility::ajax_like_link('like', $nid, 'Like');
        unset($_SESSION['like-images'][$nid]);
    };

    $response = new AjaxResponse();
    $response->addCommand( new HtmlCommand(
      '#like-action-' . $nid, 
      $like_link 
    ));
    
    return $response;
  }

}
