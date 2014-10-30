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
use Drupal\node\Entity\Node;


/**
 * Main HoneyComb
 */
class HoneyCombController {


  /*
   * Default Vendor Home page.
   * 
   * @param $nid 
   *  The company node id
   */
  public function company_profile($vendor_category, $nid) {
    $company = Node::load($nid)->toArray();
    // Need to gather all images that have been linked to this company
    $images = $this->company_images($nid);
    return array(
      '#title' => $company['title'][0]['value'], // page title
      '#theme' => 'company_profile',
      '#company' => $company,
      '#images' => $images,  // array of uri images.
      '#attached' => array (
        'library' => array (
          'honeycomb/honeycomb.image-selection',
        ),
      ),
    );
  }

  /**
   * Return Images tagged to the company
   *
   * @param $nid
   *  The nid of the company node type.
   *  
   * @return
   *  Array of images with their fid.   
   */
  public function company_images($nid) {
    $query = db_select('node__field_photographer', 'p');
    $query->leftJoin('file_managed', 'fm', 'p.entity_id = fm.fid');
    $query->fields('fm', array('uri','fid'));
    $query->condition('p.bundle', 'vendor_image');
    $query->condition('p.field_photographer_target_id', 8);
    $result = $query->execute();

    $images = array();
    foreach ($result as $record) {
      $images[$record->fid] = $record->uri;
    };
    return $images;
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

    // This would normally be the vendor category that the user is trying to find. 
    $tid = 0; 
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
        $like_link = HoneyCombUtility::ajax_like_link('like', $record->nid, $tid, 'Like');
      }
      else {
        $like_link = HoneyCombUtility::ajax_like_link('unlike', $record->nid, $tid, 'UnLike');
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
  public function like_action($action = 'like' , $nid = 0, $tid = 0 , $title = '') {
    $account = \Drupal::currentUser();

    if ($action == 'like') {
        $like_link = HoneyCombUtility::ajax_like_link('unlike', $nid, $tid, 'UnLike');
        $_SESSION['like-images'][$nid] = $nid;
        HoneyCombUtility::photo_like($account->id(), $nid, $tid);
    }
    else {
        $like_link = HoneyCombUtility::ajax_like_link('like', $nid, $tid, 'Like');
        unset($_SESSION['like-images'][$nid]);
        HoneyCombUtility::photo_unlike($account->id(), $nid, $tid);
    };

    $response = new AjaxResponse();
    $response->addCommand( new HtmlCommand(
      '#like-action-' . $nid, 
      $like_link 
    ));
    
    return $response;
  }

}
