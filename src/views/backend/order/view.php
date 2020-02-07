<?php

use yii\helpers\Url;
use yii\helpers\Html;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this \yii\web\View
 * @var $orderData \DmitriiKoziuk\yii2Shop\data\order\OrderData
 * @var $fileWebHelper \DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper
 */

$this->title = Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Order:') . ' ' . $orderData->getId();
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Order - ' . $orderData->getId();

$customer = $orderData->customer();
$cart = $orderData->cart();
$stageLog = $orderData->stageLog();
?>

<div class="order-view">
  <div class="row">
    <div class="col-md-3">
      <h3><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Customer info') ?></h3>
      <table class="table table-bordered">
        <tr>
          <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'First name') ?></td><td><?= $customer->getFirstName() ?></td>
        </tr>
        <tr>
          <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Last name') ?></td><td><?= $customer->getLastName() ?></td>
        </tr>
        <tr>
          <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Phone number') ?></td><td><?= $customer->getPhoneNumber() ?></td>
        </tr>
      </table>
      <h3><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Order info') ?></h3>
      <table class="table table-bordered">
        <tr>
          <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Total product') ?></td><td><?= $cart->getTotalProduct() ?></td>
        </tr>
        <tr>
          <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Total price') ?></td><td><?= $cart->getTotalPrice() ?></td>
        </tr>
        <tr>
          <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Order time') ?></td><td><?= $stageLog->getOrderTime() ?></td>
        </tr>
      </table>
    </div>
    <div class="col-md-9">
      <h3><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Ordered products') ?></h3>
      <table class="table table-bordered">
        <thead>
          <tr>
            <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Product ID') ?></td>
            <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Image') ?></td>
            <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Name') ?></td>
            <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Price per item') ?></td>
            <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Quantity') ?></td>
            <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Total price') ?></td>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($cart->getProducts() as $productData): ?>
          <tr>
            <td><?= $productData->getSku()->getId() ?></td>
            <td>
            <?php if ($productData->getSku()->isHasImages()): ?>
              <?= Html::img(
                  $fileWebHelper->getFileFullWebPath($productData->getSku()->getMainImage()),
                  ['style' => 'max-height: 150px;max-weight: 150px;']
                ) ?>
            <?php endif; ?>
            </td>
            <td>
                <?= Html::a(
                    $productData->getSku()->getFullName(),
                    Url::to(['product/update', 'id' => $productData->getProductId()]),
                    ['target' => '_blank']
                    ) ?>
            </td>
            <td><?= $productData->getSku()->getPriceOnSite() ?></td>
            <td><?= $productData->getQuantity() ?></td>
            <td><?= $productData->getFinalPrice() ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
