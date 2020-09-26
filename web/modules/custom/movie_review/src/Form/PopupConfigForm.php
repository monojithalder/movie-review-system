<?php

namespace Drupal\movie_review\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PopupConfigForm.
 */
class PopupConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'movie_review.popupconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'popup_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('movie_review.popupconfig');
    $popup_data = $config->get('data');
    $form['description'] = array(
      '#markup' => '<div>'. t('This example shows an add-more and a remove-last button.').'</div>',
    );
    $test = $popup_data[0]['popup_image'][0];
    $i = 0;
    $name_field = $form_state->get('num_names');
    $form['#tree'] = TRUE;
    $form['names_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('PopUp Data'),
      '#prefix' => '<div id="names-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
    if (empty($name_field) && empty($popup_data)) {
      $form_state->set('num_names', 1);
      $name_field = 1;
    }
    elseif (empty($name_field) && !empty($popup_data)) {
      $form_state->set('num_names', count($popup_data));
      $name_field = count($popup_data);
    }
    for ($i = 0; $i < $name_field; $i++) {
      $form['names_fieldset']['display_fieldset_'.$i] = [
        '#type' => 'details',
        '#group' => 'flightsearch',
        '#tree' => TRUE,
        '#title' => $this->t('PopUp Data'),
        '#prefix' => '<div id="display-fieldset-wrapper">',
        '#suffix' => '</div>',
      ];
      $form['names_fieldset']['display_fieldset_'.$i]['main_title'] = [
        '#type' => 'textfield',
        '#title' => t('Main Title'),
        '#default_value' => (isset($popup_data[$i])) ? $popup_data[$i]['main_title'] : ""
      ];
      $form['names_fieldset']['display_fieldset_'.$i]['main_body'] = [
        '#type' => 'text_format',
        '#title' => t('Main Body'),
        '#default_value' => (isset($popup_data[$i])) ? $popup_data[$i]['main_body'] : ""
      ];
      $form['names_fieldset']['display_fieldset_'.$i]['popup_image'] = [
          '#type' => 'managed_file',
          '#title' => t('PopUp Image'),
          '#upload_validators' => array(
            'file_validate_extensions' => array('gif png jpg jpeg'),
            'file_validate_size' => array(25600000),
          ),
          '#theme' => 'image_widget',
          '#preview_image_style' => 'medium',
          '#upload_location' => 'public://',
          '#default_value' => '16'
      ];
    }

    $form['action_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Actions'),
      '#prefix' => '<div id="action-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['action_fieldset']['actions']['add_name'] = [
      '#type' => 'submit',
      '#value' => t('Add More'),
      '#submit' => array('::addOne'),
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'names-fieldset-wrapper',
      ],
    ];
    //if ($name_field > 1) {
      $form['action_fieldset']['actions']['remove_name'] = [
        '#type' => 'submit',
        '#value' => t('Remove one'),
        '#submit' => ['::removeCallback'],
        '#ajax' => [
          'callback' => '::addmoreCallback',
          'wrapper' => 'names-fieldset-wrapper',
        ]
      ];
    //}
    $form_state->setCached(FALSE);
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return parent::buildForm($form, $form_state);
  }
  public function addOne(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_names');
    $add_button = $name_field + 1;
    $form_state->set('num_names', $add_button);
    $form_state->setRebuild();
  }

  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_names');
    return $form['names_fieldset'];
  }

  public function removeCallback(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_names');
    if ($name_field > 1) {
      $remove_button = $name_field - 1;
      $form_state->set('num_names', $remove_button);
    }
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $popup_data = array();
    $form_data = $form_state->getValue("names_fieldset");
    foreach ($form_data as $item) {
      $popup_data [] =$item;
    }
    $a = 0;
    $this->config('movie_review.popupconfig')
    ->set('data',$popup_data)
    ->save();
  }

}
