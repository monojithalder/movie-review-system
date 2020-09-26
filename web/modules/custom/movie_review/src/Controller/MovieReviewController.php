<?php

namespace Drupal\movie_review\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Class MovieReviewController.
 */
class MovieReviewController extends ControllerBase {

  /**
   * Search Movie.
   */
  public function search_movie() {
    $header = [
      'image' => [
        'data' => $this->t('Image'),
      ],
      'title' => [
        'data' => $this->t('Title'),
        'specifier' => 'title',
      ],
      'release_year' => [
        'data' => $this->t('Release Year'),
        'specifier' => 'field_release_date'
      ]
    ];
    $title = \Drupal::request()->query->get('title');
    $actor = \Drupal::request()->query->get('actor');
    $storage = \Drupal::entityTypeManager()->getStorage('node');

    $query = $storage->getQuery();
    $query->condition('status', \Drupal\node\NodeInterface::PUBLISHED);
    $query->condition('type', 'movie');
    if(!empty($title)) {
      $query->condition('title',$title,'CONTAINS');
      //$query->condition("test_filed","sdfds",'CONTAINS');
    }
    if(!empty($actor)) {
      $actor_query = $storage->getQuery();
      $actor_query->condition('status', \Drupal\node\NodeInterface::PUBLISHED);
      $actor_query->condition('type', 'director_cast');
      $actor_query->condition('title',$actor,'CONTAINS');
      $node_ids = $actor_query->execute();
      $actor_ids = array();
      if(!empty($node_ids)) {
        $actor_ids = array_values($node_ids);
      }
      else {
        $actor_ids = array(0);
      }
      $query->condition('field_cast',$actor_ids,'IN');
    }
    $query->pager(1);
    $nids = $query->execute();
    $date_formatter = \Drupal::service('date.formatter');
    $rows = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      $row = [];
      $image_paragraphs = $node->get('field_movie_image_paragraph')->getValue();
      $image_path = "";
      foreach ($image_paragraphs as $item) {
        $paragraph_id = $item['target_id'];
        $paragraph = Paragraph::load($paragraph_id);
        $banner = $paragraph->field_banner->value;
        if($banner == true) {
          $image_id = $paragraph->field_movie_image->target_id;
          $image_file = \Drupal\file\Entity\File::load($image_id);
          $image_file_uri = $image_file->getFileUri();
          $image_file_uri = str_replace("public://","/sites/default/files/",$image_file_uri);
          $image_path = $image_file_uri;
          break;
        }
      }
      $row[] =[
        "data" => [
          "#markup" => "<img src='".$image_path."' width='100' height='100' />"
        ]
      ];
      $row[] = $node->toLink();
      $created = strtotime($node->get('field_release_date')->value);
      $row[] = [
        'data' => [
          '#theme' => 'time',
          '#text' => $date_formatter->format($created, 'custom', 'Y'),
          '#attributes' => [
            'datetime' => $date_formatter->format($created, 'custom', 'Y'),
          ],
        ],
      ];
      $rows[] = $row;
    }

    $build['form'] = $this->formBuilder()->getForm('Drupal\movie_review\Form\MovieSearchForm');
    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No content has been found.'),
    ];

    $build['pager'] = [
      '#type' => 'pager',
    ];
    return $build;
  }

}
