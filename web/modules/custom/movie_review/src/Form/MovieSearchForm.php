<?php

namespace Drupal\movie_review\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MovieSearchForm.
 */
class MovieSearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'movie_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $title = \Drupal::request()->query->get('title');
    $form['filters'] = [
      '#type'  => 'fieldset',
      '#title' => $this->t('Filter'),
      '#open'  => true,
    ];

    $form['filters']['title'] = [
      '#title'         => 'Movie Name',
      '#type'          => 'search'
    ];
    if(!empty($title)) {
      $form['filters']['title']['#default_value'] = $title;
    }
    $form['filters']['actions'] = [
      '#type'       => 'actions'
    ];

    $form['filters']['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('Filter')

    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    $field = $form_state->getValues();
    $title = $field["title"];
    $url = \Drupal\Core\Url::fromRoute('movie_review.movie_review_controller_search_movie')
      ->setRouteParameters(array('title'=>$title));
    $form_state->setRedirectUrl($url);
  }

}
