<?php

namespace Drupal\farm_opentrees\Plugin\QuickForm;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\farm_quick\Plugin\QuickForm\QuickFormBase;

/**
 * Tree claim quick form.
 *
 * @QuickForm(
 *   id = "tree_claim",
 *   label = @Translation("Tree claim"),
 *   description = @Translation("Record a claim associated with a tree."),
 *   permissions = {
 *     "create tree asset",
 *     "create observation log",
 *     "create seeding log",
 *     "create transplanting log",
 *   }
 * )
 */
class TreeClaimForm extends QuickFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Claim type'),
      '#description' => $this->t('Choose the type of claim.'),
      '#options' => [
        'seeding' => $this->t('Seeding'),
        'transplanting' => $this->t('Transplanting'),
        'observation' => $this->t('Observation'),
      ],
      '#required' => TRUE,
    ];

    $form['timestamp'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Claim date'),
      '#required' => TRUE,
      '#default_value' => new DrupalDateTime('midnight'),
    ];

    $form['tree'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Tree asset'),
      '#target_type' => 'asset',
      '#target_bundle' => 'tree',
      '#required' => TRUE,
    ];

    $quantities = [
      'height' => [
        'label' => $this->t('Height'),
        'measure' => 'length',
        'units' => 'm',
      ],
      'trunk' => [
        'label' => $this->t('Trunk diameter'),
        'measure' => 'length',
        'units' => 'm',
      ],
      'health' => [
        'label' => $this->t('Health rating'),
        'measure' => 'rating',
      ],
    ];
    foreach ($quantities as $id => $quantity_info) {
      $form[$id] = [
        '#type' => 'number',
        '#title' => $quantity_info['label']
      ];
    }

    $form['notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Notes'),
      '#description' => $this->t('Note about the claim event.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    # TODO: Add submit.
  }

}
