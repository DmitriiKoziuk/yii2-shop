<?php

use yii\web\View;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\data\CategoryData;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\Brand;
use DmitriiKoziuk\yii2Shop\widgets\SubcategoriesWidget;
use DmitriiKoziuk\yii2Shop\widgets\ProductWidget;
use DmitriiKoziuk\yii2Shop\widgets\frontend\CategoryProductFacetedNavigationWidget;
use DmitriiKoziuk\yii2Shop\widgets\ProductBrandWidget;

/**
 * @var $this View
 * @var $categoryData CategoryData
 * @var $facetedAttributes EavAttributeEntity[]
 * @var $indexPageUrl string
 * @var $getParams array|null
 * @var $filterParams array,
 * @var $filteredAttributes EavAttributeEntity[]
 * @var $productDataProvider ActiveDataProvider
 * @var $brands Brand[]
 */

$this->title = $categoryData->getMetaTitle();
$this->registerMetaTag(['name' => 'description', 'content' => $categoryData->getMetaDescription()]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $categoryData->getUrl()]);
$this->params['breadcrumbs'] = $categoryData->getBreadcrumb();
?>
<div class="category">
  <div class="row">
    <div class="col-md-3">
      <?= SubcategoriesWidget::widget([
          'category' => $categoryData,
      ]) ?>

      <?= ProductBrandWidget::widget([
          'brands' => $brands,
          'indexPageUrl' => $indexPageUrl,
      ]) ?>

      <?= CategoryProductFacetedNavigationWidget::widget([
          'facetedAttributes' => $facetedAttributes,
          'filteredAttributes' => $filteredAttributes,
          'indexPageUrl' => $indexPageUrl,
          'getParams' => $getParams,
      ]) ?>
    </div>
    <div class="col-md-9">
      <?= ProductWidget::widget([
          'productDataProvider' => $productDataProvider,
          'indexPageUrl' => $indexPageUrl,
          'filterParams' => $filterParams,
      ]) ?>
    </div>
  </div>
</div>
