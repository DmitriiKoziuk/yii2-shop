<?php

use yii\web\View;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\helpers\CategoryProductFacetedNavigationHelper;

/**
 * @var $this View
 * @var $attributes EavAttributeEntity[]
 * @var $indexPageUrl string
 * @var $getParams array|null
 */

?>

<div class="faceted-navigation">
  <?php foreach ($attributes as $attribute): ?>
  <div class="attribute-tile">
    <?= $attribute->name ?>
  </div>
  <ul class="attribute-values">
    <?php foreach ($attribute->values as $value): ?>
    <li class="attribute-value">
      <a href="<?= CategoryProductFacetedNavigationHelper::createUrl(
          $indexPageUrl,
          $attribute,
          $value
      ) ?>">
        <?= $value->value ?>
        <?= ! empty($value->unit) ? $value->unit->name : ''; ?>
        ( <?= $value->count ?> )
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endforeach; ?>
</div>
