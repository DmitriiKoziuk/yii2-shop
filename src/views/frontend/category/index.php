<?php

use yii\web\View;
use DmitriiKoziuk\yii2Shop\data\CategoryData;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\widgets\ProductWidget;
use DmitriiKoziuk\yii2Shop\widgets\frontend\CategoryProductFacetedNavigationWidget;

/**
 * @var $this View
 * @var $categoryData CategoryData
 * @var $facetedAttributes EavAttributeEntity[]
 * @var $indexPageUrl string
 * @var $getParams array|null
 * @var $filteredAttributes EavAttributeEntity[]
 */

$productSearchParams = new ProductSearchParams();
$productSearchParams->category_id = $categoryData->getId();
$productSearchParams->stock_status = [ProductSku::STOCK_IN, ProductSku::STOCK_AWAIT];

$this->title = $categoryData->getMetaTitle();
$this->registerMetaTag(['name' => 'description', 'content' => $categoryData->getMetaDescription()]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $categoryData->getUrl()]);
?>
<div class="category">
  <div class="row">
    <div class="col-md-3">
      <?= CategoryProductFacetedNavigationWidget::widget([
          'facetedAttributes' => $facetedAttributes,
          'filteredAttributes' => $filteredAttributes,
          'indexPageUrl' => $indexPageUrl,
          'getParams' => $getParams,
      ]) ?>
    </div>
    <div class="col-md-7">
      <?= ProductWidget::widget([
          'searchParams' => $productSearchParams,
          'indexPageUrl' => $indexPageUrl,
          'filteredAttributes' => $filteredAttributes,
      ]) ?>
    </div>
  </div>
</div>