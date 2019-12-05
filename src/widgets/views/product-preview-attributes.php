<?php

use yii\web\View;
use DmitriiKoziuk\yii2Shop\entityViews\ProductEntityView;
use DmitriiKoziuk\yii2Shop\entityViews\ProductSkuView;

/**
 * @var $this View
 * @var $product ProductEntityView|ProductSkuView
 */
?>

<?php if ($product->isPreviewEavValuesSet()): ?>
  <?php foreach ($product->getProductPreviewValues() as $value): ?>
  <div>
    <?= $value->eavAttribute->name ?>: <?= $value->value ?> <?= !empty($value->unit) ? $value->unit->abbreviation : ''; ?>
  </div>
  <?php endforeach; ?>
<?php endif; ?>
