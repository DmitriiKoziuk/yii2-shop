<?php

use yii\web\View;
use DmitriiKoziuk\yii2Shop\data\frontend\product\ProductData;
use DmitriiKoziuk\yii2Shop\data\frontend\product\ProductSkuData;

/**
 * @var $this View
 * @var $product ProductData|ProductSkuData
 */
?>

<?php if ($product->isPreviewAttributesSet()): ?>
  <?php foreach ($product->getProductPreviewValues() as $value): ?>
  <div>
    <?= $value->eavAttribute->name ?>: <?= $value->value ?> <?= !empty($value->unit) ? $value->unit->abbreviation : ''; ?>
  </div>
  <?php endforeach; ?>
<?php endif; ?>
