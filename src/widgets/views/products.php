<?php

use yii\web\View;
use yii\data\Pagination;
use DmitriiKoziuk\yii2Shop\assets\frontend\BaseAsset;
use DmitriiKoziuk\yii2Shop\assets\frontend\ProductWidgetAsset;
use DmitriiKoziuk\yii2Shop\data\frontend\product\ProductData;
use DmitriiKoziuk\yii2Shop\data\frontend\product\ProductSkuData;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\widgets\frontend\LinkPagerWidget;

/**
 * @var $this View
 * @var $products ProductData[]|ProductSkuData[]
 * @var $pagination Pagination
 * @var $indexPageUrl string
 */

ProductWidgetAsset::register($this);

$defaultImageUrl = $this->assetManager
  ->getBundle(BaseAsset::class)->baseUrl . BaseAsset::$defaultImageWebPath;
?>

<div class="row products">
  <?php foreach ($products as $product): ?>
  <div class="col-md-4 product">
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-12">
            <div class="image">
              <a href="<?= $product->getUrl() ?>">
                <?php if (! empty($product->isMainImageSet())): ?>
                <img src="<?= $product->getMainImage()->getThumbnail(100, 100) ?>" alt="<?= $product->getFullName() ?>">
                <?php else: ?>
                <img src="<?= $defaultImageUrl ?>" alt="">
                <?php endif; ?>
              </a>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="title">
              <a href="<?= $product->getUrl() ?>">
                <span class="type-name"><?= $product->getTypeName() ?></span>
                <?= $product->getFullName() ?>
              </a>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="price-scope">
              <div class="row align-items-center">
                <div class="col-md-6">
                  <div class="price">
                    <?php if ($product->isPriceSet()): ?>
                    <?= number_format(
                        $product->getPrice(),
                        0,
                        '.',
                        ' '
                    ) ?>
                    <span class="currency">currency</span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <?php if ($product->isPriceSet()): ?>
                  <a class="btn buy-button" href="/cart/add-product?product=<?= $product->getId() ?>">
                    <?= Yii::t(ShopModule::TRANSLATION, 'Buy') ?>
                  </a>
                  <?php endif; ?>
                  <a class="btn favorite-button" href="/favorite/add?product=<?= $product->getId() ?>">
                    F
                  </a>
                  <a class="btn compare-button" href="/compare/add?product=<?= $product->getId() ?>">
                    C
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
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
    ]) ?>
  </div>
</div>
