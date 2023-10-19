<?php

namespace Drupal\beer_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SearchBeerForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'search_beer_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $dish = NULL) {
    $form['dish'] = [
      '#type' => 'textfield',
      '#required'  => TRUE,
      '#default_value' => $dish,
      '#title' => $this->t('Enter your dish'),
    ];

    $form['actions']['#type'] = 'actions';

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('beer_api.search_page', [
      'dish' => $form_state->getValue('dish'),
    ]);
  }
}
