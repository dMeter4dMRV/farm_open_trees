<?php

namespace Drupal\farm_opentrees\Controller;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * OpenTree controller.
 */
class OpenTree extends ControllerBase {

  /**
   * @param \Drupal\asset\Entity\AssetInterface $asset
   */
  public function data(AssetInterface $asset) {

    // Return json data for OpenTrees CO2 asset.
    $registration =  new DrupalDateTime($asset->getCreatedTime());
    $registration = $registration->format('m-d-Y');
    $fields = [
      "Id" => $asset->uuid(),
      "Name" => $asset->getName(),
      "GeoCid" => $asset->get('intrinsic_geometry')->value,
      "TreeStage" => null,
      "Images" => null,
      "Documents" => null,
      "IsNewTree" => $asset->get('is_new_tree')->value ?? FALSE,
      "Description" => $asset->get('description')->value,
      "SpeciesName" => $asset->get('tree_type')->getEntity()->label(),
      "CategoryName" => null,
      "ProjectStatus" => null,
      "ScientificName" => null,
      "RegistrationDate" => $registration,
    ];
    return JsonResponse::create($fields);
  }

}
