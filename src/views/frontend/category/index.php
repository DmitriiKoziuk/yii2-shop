<?php

use DmitriiKoziuk\yii2Shop\widgets\ProductWidget;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;

/**
 * @var $this \yii\web\View
 * @var $categoryData \DmitriiKoziuk\yii2Shop\data\CategoryData
 * @var $filterService \DmitriiKoziuk\yii2CustomUrls\services\UrlFilterService
 */

$productSearchParams = new ProductSearchParams();
$productSearchParams->category_id = $categoryData->getId();

$this->title = $categoryData->getMetaTitle();
$this->registerMetaTag(['name' => 'description', 'content' => $categoryData->getMetaDescription()]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $categoryData->getUrl()]);
?>
<div class="category">
  <div class="row">
    <div class="col-md-12">
      <?= ProductWidget::widget([
          'searchParams' => $productSearchParams,
      ]) ?>
    </div>
  </div>
</div>