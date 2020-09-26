<?php

namespace Drupal\movie_review\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'PopupBlock' block.
 *
 * @Block(
 *  id = "popup_block",
 *  admin_label = @Translation("Popup block"),
 * )
 */
class PopupBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $popup_data = \Drupal::config('movie_review.popupconfig')->get('data');
    $user = \Drupal::currentUser();
    $username = $user->getAccountName();
    $message = "Hello ".$username;
    foreach ($popup_data as $key => $popup_datum) {
      $image_id = $popup_datum['popup_image'][0];
      $image_file = \Drupal\file\Entity\File::load($image_id);
      $image_file_uri = "";
      if(empty(!$image_file)) {
        $image_file_uri = $image_file->getFileUri();
        $image_file_uri = str_replace("public://", "/sites/default/files/", $image_file_uri);
      }
      $popup_data[$key]['popup_image'] = $image_file_uri;
    }
    return array(
      '#data' => $popup_data,
      '#message' => $message,
      '#theme' => 'popup_block'
    );
  }

}
