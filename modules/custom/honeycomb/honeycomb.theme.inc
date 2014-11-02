<?php

/**
 * @file
 * Custom hook into the honeycomb system.
 */
use Drupal\taxonomy\Entity\Term;
Use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Url;

use Drupal\honeycomb\HoneyCombUtility;


/**
 * Perpares veriables for company profile template
 *
 * Default template: masonry-image.html.twig 
 * 
 * @param array $variables;
 *  - image_uri: the path to the image.
 *  - image_nid: the {node}.nid of the vendor_image content type.
 *  - image_category_id: The term id of the of the category that the image belongs to.
 */
function template_preprocess_image_colorbox_modal(&$variables) {

  $image_url = $variables['image_uri'];    $image_opts = array(
    '#theme' => 'image',
    '#uri' => $image_url,
    '#attributes' => array(
      'class' => 'modal',
    ),
  );
  $image_link = drupal_render($image_opts);

  if (empty($_SESSION['like-images'][$variables['image_nid']])) {
    $like_link = HoneyCombUtility::ajax_like_link('like', $variables['image_nid'], $variables['image_category_id'], 'Like');
  }
  else {
    $like_link = HoneyCombUtility::ajax_like_link('unlike', $variables['image_nid'], $variables['image_category_id'], 'UnLike');
  };
  
  $variables['like_link'] = $like_link;
  $variables['image_link'] = $image_link;

}

/**
 * Perpares veriables for company profile template
 *
 * Default template: masonry-image.html.twig 
 * 
 * @param array $variables;
 *  - image_uri: the path to the image.
 *  - image_nid: the {node}.nid of the vendor_image content type.
 *  - image_category_id: The term id of the of the category that the image belongs to.
 */
function template_preprocess_masonry_image(&$variables) {

  $image_url = ImageStyle::load('image_masonry')->buildUrl($variables['image_uri']);
  $path = file_create_url($variables['image_uri']); // full image url
  $options = array(
    'html' => TRUE,
    'absolute' => TRUE,
    'query' => array(
      'imgurl' => serialize($path),
    ),
    'attributes' => array(
      'class' => array('colorbox'),
    )
  );
  $full_url = Url::fromRoute('honeycomb.modal_image',array('image_id' => $variables['image_nid']), $options);

  $image_opts = array(
    '#theme' => 'image',
    '#uri' => $image_url,
  );
  $image = drupal_render($image_opts);
  $image_link = \Drupal::l($image, $full_url, array('html' => TRUE));

  if (empty($_SESSION['like-images'][$variables['image_nid']])) {
    $like_link = HoneyCombUtility::ajax_like_link('like', $variables['image_nid'], $variables['image_category_id'], 'Like');
  }
  else {
    $like_link = HoneyCombUtility::ajax_like_link('unlike', $variables['image_nid'], $variables['image_category_id'], 'UnLike');
  };
  
  $variables['like_link'] = $like_link;
  $variables['image_link'] = $image_link;

}
/**
 * Perpares veriables for company profile template
 *
 * Default template: company-profile.html.twig 
 * 
 * @param array $variables;
 *  An array containing the following elements:
 *  - title: The company name
 *  - nid: The id of the company
 */
function template_preprocess_company_profile(&$variables) {
  $company = $variables['company'];
  if ($company) {
    $tid =$company['taxonomy_vendor_category'][0]['target_id'];
    $term = Term::load($tid);
    // Logo
    $fid =$company['field_image'][0]['target_id'];
    $image_uri = File::load($fid)->toArray()['uri'][0]['value'];
    $variables['logo_url'] = ImageStyle::load('medium')->buildUrl($image_uri);

    $variables['nid'] =$company['nid'][0]['value'];
    $variables['title'] =$company['title'][0]['value'];
    $variables['body'] =$company['body'][0]['value'];
    $variables['vendor_category'] = $term->getName();

    // Vendor Images attached to this Company
    if (is_array($variables['images'])) {
      foreach ($variables['images'] as $fid => $uri) {
        $variables['image_urls'][$fid] = ImageStyle::load('image_masonry')->buildUrl($uri);
      };
    };
  };


};
