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
      <h3>Customer info</h3>
      <table class="table table-bordered">
        <tr>
          <td>First name</td><td><?= $customer->getFirstName() ?></td>
        </tr>
        <tr>
          <td>Last name</td><td><?= $customer->getLastName() ?></td>
        </tr>
        <tr>
          <td>Phone number</td><td><?= $customer->getPhoneNumber() ?></td>
        </tr>
      </table>
      <h3>Order info</h3>
      <table class="table table-bordered">
        <tr>
          <td>Total product</td><td><?= $cart->getTotalProduct() ?></td>
        </tr>
        <tr>
          <td>Total price</td><td><?= $cart->getTotalPrice() ?></td>
        </tr>
        <tr>
          <td>Order time</td><td><?= $stageLog->getOrderTime() ?></td>
        </tr>
      </table>
    </div>
    <div class="col-md-9">
      <h3>Ordered products</h3>
      <table class="table table-bordered">
        <thead>
          <tr>
            <td>Product ID</td>
            <td>Image</td>
            <td>Name</td>
            <td>Price per item</td>
            <td>Quantity</td>
            <td>Total price</td>
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
                    Url::to(['product/update', 'id' => $productData->getSku()->getId()]),
                    ['target' => '_blank']
                    ) ?>
            </td>
            <td><?= $productData->getSku()->getPrice() ?></td>
            <td><?= $productData->getQuantity() ?></td>
            <td><?= $productData->getFinalPrice() ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
