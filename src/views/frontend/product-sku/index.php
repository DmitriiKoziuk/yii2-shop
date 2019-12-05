<?php

use yii\web\View;
use DmitriiKoziuk\yii2Shop\entityViews\ProductSkuView;
use DmitriiKoziuk\yii2Shop\assets\frontend\BaseAsset;
use DmitriiKoziuk\yii2Shop\assets\frontend\ProductSkuAsset;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\widgets\frontend\ProductSkuViewAttributesWidget;
use DmitriiKoziuk\yii2Shop\services\product\ProductSeoService;

/**
 * @var $this View
 * @var $productSkuView ProductSkuView
 * @var $fileWebHelper FileWebHelper
 * @var $productSeoService ProductSeoService
 */

ProductSkuAsset::register($this);

$this->title = $productSeoService->getProductSkuMetaTitle($productSkuView);
$this->registerMetaTag([
    'name' => 'description',
    'content' => $productSeoService->getProductSkuMetaDescription($productSkuView),
]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $productSkuView->getUrl()]);

$defaultImageUrl = $this->assetManager
    ->getBundle(BaseAsset::class)->baseUrl . BaseAsset::$defaultImageWebPath;
$productFullName = $productSkuView->getProductName() . ' ' . $productSkuView->getName();
?>

<div class="product-sku">
  <div class="row">
    <div class="col-md-12">
      <h1><?= $productFullName ?></h1>
      <div class="row">
        <div class="col-md-6 image-section">
          <?php if (! empty($mainImage)): ?>
          <img src="<?= $fileWebHelper->getFileFullWebPath($mainImage) ?>" alt="<?= $productFullName ?>">
          <?php else: ?>
          <img src="<?= $defaultImageUrl ?>" alt="<?= $productFullName ?>">
          <?php endif; ?>
        </div>
        <div class="col-md-6">
          <?php if($productSkuView->isCustomerPriceSet()): ?>
          <div class="price">
            <?= number_format(
                $productSkuView->getCustomerPrice(),
                0,
                '.',
                ' '
            ) ?>
            <span class="currency">$</span>
          </div>
          <div class="buttons">
            <a class="btn buy-button" href="/cart/add-product?product=<?= $productSkuView->getId() ?>">
              <?= Yii::t(ShopModule::TRANSLATION, 'Buy') ?>
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <?= ProductSkuViewAttributesWidget::widget([
              'productSkuId' => $productSkuView->getId(),
          ]) ?>
        </div>
      </div>
    </div>
  </div>
</div>

