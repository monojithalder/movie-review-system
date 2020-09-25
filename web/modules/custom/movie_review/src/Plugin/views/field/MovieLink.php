<?php

/**
 * @file
 * Definition of Drupal\general_views_alter\Plugin\views\field\TrainingSchedule
 */

namespace Drupal\movie_review\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\display\DisplayPluginBase;

/**
 * Field handler to flag the node type.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("movie_link")
 */
class MovieLink extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Define the available options
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
//    $options['node_type'] = array('default' => 'article');

    return $options;
  }
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->currentDisplay = $view->current_display;
  }

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $id = $values->id;
    $return_string = "";
    $query = \Drupal::service('entity.query');
    $node_ids = $query->get('node')
      ->condition('type', 'movie')
      ->condition('field_movie_image_paragraph',$id)
      ->condition('status', 1)  // Once we have our conditions specified we use the execute() method to run the query
      ->execute();
    $node_id = 0;
    if(!empty($node_ids)) {
      $node_ids = array_values($node_ids);
      $node_id = $node_ids[0];
      $return_string .= "/node/".$node_id;
    }
    return $return_string;
  }
}
