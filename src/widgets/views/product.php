<?php

use yii\web\View;
use yii\data\Pagination;
use DmitriiKoziuk\yii2Shop\assets\frontend\BaseAsset;
use DmitriiKoziuk\yii2Shop\data\frontend\product\ProductData;
use DmitriiKoziuk\yii2Shop\data\frontend\product\ProductSkuData;
use DmitriiKoziuk\yii2Shop\widgets\ProductPreviewAttributesWidget;
use DmitriiKoziuk\yii2Shop\widgets\frontend\LinkPagerWidget;

/**
 * @var $this View
 * @var $products ProductData[]|ProductSkuData[]
 * @var $pagination Pagination
 * @var $indexPageUrl string
 * @var $filterParams array
 */

$defaultImageUrl = $this->assetManager
  ->getBundle(BaseAsset::class)->baseUrl . BaseAsset::$defaultImageWebPath;
?>

<div class="row">
  <?php foreach ($products as $product): ?>
    <div class="col-md-4">
      <div class="thumbnail">
        <a href="<?= $product->getUrl() ?>">
          <?php if (! empty($product->isMainImageSet())): ?>
          <img src="<?= $product->getMainImage()->getThumbnail(200, 200) ?>" alt="<?= $product->getFullName() ?>">
          <?php else: ?>
          <img src="<?= $defaultImageUrl ?>" alt="">
          <?php endif; ?>
        </a>
        <div class="caption">
          <h4>
            <span class="type-name"><?= $product->getTypeName() ?></span>
              <?= $product->getFullName() ?>
          </h4>
          <p>
            <?php if ($product->isPriceSet() && $product->isCurrencySet()): ?>
              <?= number_format(
                  $product->getPrice(),
                  0,
                  '.',
                  ' '
              ) ?>
              <span class="currency"><?= $product->getCurrencySymbol() ?></span>
            <?php endif; ?>
          </p>
          <p>
            <a href="<?= $product->getUrl() ?>" class="btn btn-primary" role="button">View</a>
            <a href="/cart/add-product?product=<?= $product->getId() ?>" class="btn btn-default" role="button">Buy</a>
          </p>
          <p>
            <?= ProductPreviewAttributesWidget::widget([
                'product' => $product,
            ]) ?>
          </p>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row">
  <div class="col-md-12">
    <?= LinkPagerWidget::widget([
        'pagination' => $pagination,
        'indexPageUrl' => $indexPageUrl,
        'filterParams' => $filterParams,
    ]) ?>
  </div>
</div>
