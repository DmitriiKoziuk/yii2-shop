<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\assets\frontend\CartViewAsset;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;

/**
 * @var $this \yii\web\View
 * @var $cart \DmitriiKoziuk\yii2Shop\data\CartData
 * @var $fileWebHelper FileWebHelper
 */

CartViewAsset::register($this);
?>

<div class="container cart">
<?php $form = ActiveForm::begin(['action' => '/cart/update']) ?>
  <div class="row">
    <div class="col-md-9">
      <div class="row products">
        <div class="col-md-12">
          <?php foreach ($cart->getProducts() as $cartProduct): ?>
            <?php $productSku = $cartProduct->getSku(); ?>
            <div class="row product">
              <div class="col-md-2 main-image-wrapper">
                <?php if ($productSku->isHasImages()): ?>
                <img src="<?= $fileWebHelper->getFileFullWebPath(
                    $productSku->getMainImage()
                ) ?>" class="main-image" alt="">
                <?php endif; ?>
              </div>
              <div class="col-md-4">
                <div class="name">
                  <span class="name"><?= $productSku->getFullName() ?></span>
                </div>
                <div class="item-price">
                  <span class="price"><?= $productSku->getPrice() ?></span>
                </div>
              </div>
              <div class="col-md-2">
                <span class="quantity">
                  <?= Html::input('text', "quantity[{$productSku->getId()}]", $cartProduct->getQuantity()) ?>
                </span>
              </div>
              <div class="col-md-2">
                <div class="final-price">
                  <?= $cartProduct->getFinalPrice() ?>
                </div>
              </div>
              <div class="col-md-2">
                <div class="delete-link">
                  <a href="<?= Url::to(['/cart/remove-product', 'id' => $productSku->getId()]) ?>">удалить</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="col-md-3 actions">
      <div class="total-price-wrapper">
        <span class="total-price">
          <?= $cart->getTotalPrice(); ?>
        </span>
      </div>
      <div>
          <?= Html::submitButton(Yii::t(ShopModule::TRANSLATION_CART, 'Update quantity')) ?>
      </div>
      <div>
          <?= Html::a(Yii::t(ShopModule::TRANSLATION_CART, 'Checkout'), '/cart/checkout') ?>
      </div>
    </div>
  </div>
<?php ActiveForm::end() ?>
</div>
