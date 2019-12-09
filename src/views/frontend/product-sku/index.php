<?php declare(strict_types=1);

use yii\web\View;
use DmitriiKoziuk\yii2Shop\entityViews\ProductSkuView;
use DmitriiKoziuk\yii2Shop\assets\frontend\BaseAsset;
use DmitriiKoziuk\yii2Shop\assets\frontend\ProductSkuAsset;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\widgets\frontend\ProductSkuViewAttributesWidget;
use DmitriiKoziuk\yii2Shop\services\product\ProductSeoService;

/**
 * @var $this View
 * @var $productSkuView ProductSkuView
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
?>

<div class="product-sku">
  <div class="row">
    <div class="col-md-12">
      <h1><?= $productSkuView->getFullName() ?></h1>
      <div class="row">
        <div class="col-md-6 image-section">
          <?php if ($productSkuView->isMainImageSet()): ?>
            <img src="<?= $productSkuView->getMainImage()->getThumbnail(200, 200) ?>" alt="<?= $productSkuView->getFullName() ?>">
          <?php else: ?>
          <img src="<?= $defaultImageUrl ?>" alt="<?= $productSkuView->getFullName() ?>">
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
              'values' => $productSkuView->getEavValues(),
          ]) ?>
        </div>
      </div>
    </div>
  </div>
</div>
