<?php

use yii\web\View;
use yii\data\Pagination;
use DmitriiKoziuk\yii2Shop\assets\frontend\BaseAsset;
use DmitriiKoziuk\yii2Shop\entityViews\ProductEntityView;
use DmitriiKoziuk\yii2Shop\entityViews\ProductSkuView;
use DmitriiKoziuk\yii2Shop\widgets\ProductPreviewAttributesWidget;
use DmitriiKoziuk\yii2Shop\widgets\frontend\LinkPagerWidget;

/**
 * @var $this View
 * @var $products ProductEntityView[]|ProductSkuView[]
 * @var $pagination Pagination
 * @var $indexPageUrl string
 * @var $filterParams array
 */

$defaultImageUrl = $this->assetManager
  ->getBundle(BaseAsset::class)->baseUrl . BaseAsset::$defaultImageWebPath;
?>

<div class="row">
  <?php foreach ($products as $product): ?>
    <div class="col-md-4 card" style="padding-left: 0; padding-right: 0; margin-bottom: 30px;">
      <a href="<?= $product->getUrl() ?>">
          <?php if (! empty($product->isMainImageSet())): ?>
            <img class="card-img-top" src="<?= $product->getMainImage()->getThumbnail(200, 200) ?>" alt="<?= $product->getFullName() ?>">
          <?php else: ?>
            <img class="card-img-top" src="<?= $defaultImageUrl ?>" alt="">
          <?php endif; ?>
      </a>
      <div class="card-body">
        <h4 class="card-title">
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
          <a href="<?= $product->getUrl() ?>" class="btn btn-primary">View</a>
          <a href="/cart/add-product?product=<?= $product->getId() ?>" class="btn btn-default">Buy</a>
        </p>
      </div>
      <?= ProductPreviewAttributesWidget::widget([
          'product' => $product,
      ]) ?>
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
