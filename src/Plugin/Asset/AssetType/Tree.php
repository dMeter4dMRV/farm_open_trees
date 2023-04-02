<?php

namespace Drupal\farm_opentrees\Plugin\Asset\AssetType;

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\entity\BundleFieldDefinition;
use Drupal\farm_entity\Plugin\Asset\AssetType\AssetTypeBase;

/**
 * Provides the tree asset type.
 *
 * @AssetType(
 *   id = "tree",
 *   label = @Translation("Tree"),
 * )
 */
class Tree extends AssetTypeBase {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = parent::buildFieldDefinitions();

    $fields['registration_date'] = BundleFieldDefinition::create('datetime')
      ->setLabel($this->t('Registration date'))
      ->setDescription($this->t('The date the tree was registered.'))
      ->setRevisionable(TRUE)
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'datetime_default',
        'label' => 'inline',
        'settings' => [
          'format_type' => 'html_date',
        ],
      ]);

    $field_info = [
      'tree_type' => [
        'type' => 'entity_reference',
        'label' => $this->t('Species'),
        'description' => "Enter the tree species.",
        'target_type' => 'taxonomy_term',
        'target_bundle' => 'plant_type',
        'auto_create' => TRUE,
        'required' => TRUE,
        'multiple' => TRUE,
      ],
      'is_new_tree' => [
        'type' => 'boolean',
        'label'=> $this->t('New Tree'),
        'description' => $this->t('Is the tree new when registered?'),
        'default_value' => TRUE,
      ],
      'description' => [
        'type' => 'string_long',
        'label' => $this->t('Description'),
        'description' => $this->t('Description about this tree.'),
      ],
      'co2_cid' => [
        'type' => 'string',
        'label' => $this->t('CO2.Storage CID'),
        'multiple' => TRUE,
        'hidden' => 'form',
      ],
    ];
    foreach ($field_info as $name => $info) {
      $fields[$name] = $this->farmFieldFactory->bundleFieldDefinition($info);
    }
    return $fields;
  }

}
