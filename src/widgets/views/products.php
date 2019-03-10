<?php

use DmitriiKoziuk\yii2Shop\assets\frontend\ProductWidgetAsset;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this \yii\web\View
 * @var $products \DmitriiKoziuk\yii2Shop\data\ProductData[]
 * @var $pagination \yii\data\Pagination
 * @var $fileWebHelper FileWebHelper
 */

ProductWidgetAsset::register($this);
?>

<div class="row products">
  <?php foreach ($products as $product): ?>
  <div class="col-md-4 product">
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-12">
            <div class="image">
              <a href="<?= $product->mainSku->getUrl() ?>">
                <?php if (! empty($product->mainImage)): ?>
                <img src="<?= $fileWebHelper->getFileFullWebPath(
                    $product->mainImage
                ) ?>" alt="">
                <?php endif; ?>
              </a>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="title">
              <a href="<?= $product->mainSku->getUrl() ?>">
                <span class="type-name"><?= !empty($product->type) ? $product->type->getName() : '' ?></span>
                <?= $product->getName() ?> <?= $product->mainSku->getName() ?>
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
                    <?php if ($product->mainSku->isPriceOnSiteSet()): ?>
                    <?= number_format(
                        $product->mainSku->getPriceOnSite(),
                        0,
                        '.',
                        ' '
                    ) ?>
                    <span class="currency">currency</span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <?php if ($product->mainSku->isPriceOnSiteSet()): ?>
                  <a class="btn buy-button" href="/cart/add-product?product=<?= $product->mainSku->getId() ?>">
                    <?= Yii::t(ShopModule::TRANSLATION, 'Buy') ?>
                  </a>
                  <?php endif; ?>
                  <a class="btn favorite-button" href="/favorite/add?product=<?= $product->mainSku->getId() ?>">
                    F
                  </a>
                  <a class="btn compare-button" href="/compare/add?product=<?= $product->mainSku->getId() ?>">
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
