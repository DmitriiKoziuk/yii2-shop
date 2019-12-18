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
<ul class="list-group list-group-flush">
  <?php foreach ($product->getProductPreviewValues() as $value): ?>
  <li class="list-group-item">
      <?= $value->eavAttribute->name ?>: <?= $value->value ?> <?= !empty($value->unit) ? $value->unit->abbreviation : ''; ?>
  </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
