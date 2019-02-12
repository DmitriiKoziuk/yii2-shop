<?php

use DmitriiKoziuk\yii2Shop\assets\frontend\ProductSkuAsset;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;

/**
 * @var $this \yii\web\View
 * @var $productSkuData \DmitriiKoziuk\yii2Shop\data\ProductSkuData
 * @var $productData \DmitriiKoziuk\yii2Shop\data\ProductData
 * @var $productTypeData \DmitriiKoziuk\yii2Shop\data\ProductTypeData|null
 * @var $images \DmitriiKoziuk\yii2FileManager\entities\File[]
 * @var $mainImage \DmitriiKoziuk\yii2FileManager\entities\File|null
 * @var $fileWebHelper FileWebHelper
 */

ProductSkuAsset::register($this);

$this->title = $productSkuData->getMetaTitle();
$this->registerMetaTag(['name' => 'description', 'content' => $productSkuData->getMetaDescription()]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $productSkuData->getUrl()]);
?>

<div class="product-sku">
  <div class="row">
    <div class="col-12">
      <div class="title">
        <h1><?= $productData->getName() . ' ' . $productSkuData->getName() ?></h1>
      </div>
      <div class="row">
        <div class="col-6 image-section">
            <?php if (! empty($mainImage)): ?>
              <img src="<?= $fileWebHelper->getFileFullWebPath($mainImage) ?>" alt="">
            <?php endif; ?>
        </div>
        <div class="col-6 buy-section">
          <div class="price">
            <?= number_format(
                $productSkuData->getPriceOnSite(),
                0,
                '.',
                ' '
            ) ?>
            <span class="currency">грн.</span>
          </div>
          <div class="buttons">
            <a class="btn buy-button" href="/cart/add-product?product=<?= $productSkuData->getId() ?>">
              <i class="fas fa-shopping-cart"></i> Купить
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

