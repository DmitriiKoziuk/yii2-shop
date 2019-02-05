<?php

use DmitriiKoziuk\yii2Shop\assets\frontend\CategoryAsset;
use DmitriiKoziuk\yii2Shop\widgets\ProductWidget;

/**
 * @var $this \yii\web\View
 * @var $category \DmitriiKoziuk\yii2Shop\entities\Category
 * @var $searchParams \DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams
 */

CategoryAsset::register($this);
?>
<div class="category">
  <div class="row">
    <div class="col-3">
      filter
    </div>
    <div class="col-9">
      <?= ProductWidget::widget([
          'searchParams' => $searchParams,
      ]) ?>
    </div>
  </div>
</div>