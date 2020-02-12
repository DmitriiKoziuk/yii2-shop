<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2UserManager\entities\User;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\entities\Order;
use DmitriiKoziuk\yii2Shop\entities\OrderStageLog;
use DmitriiKoziuk\yii2Shop\forms\order\OrderUpdateStatusForm;

/**
 * @var $this View
 * @var $order Order
 * @var $fileWebHelper FileWebHelper
 * @var $updateStatusForm OrderUpdateStatusForm
 * @var $users User[]
 */

$this->title = Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Order:') . ' ' . $order->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Order - ' . $order->id;

$customer = $order->cart->customer;
$cart = $order->cart;
$currentStage = $order->currentStage;
?>

<div class="order-view">
  <div class="row">
    <div class="col-md-3">
      <h3><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Customer info') ?></h3>
      <table class="table table-bordered">
        <tr>
          <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'First name') ?></td><td><?= $customer->first_name ?></td>
        </tr>
        <tr>
          <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Last name') ?></td><td><?= $customer->last_name ?></td>
        </tr>
        <tr>
          <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Phone number') ?></td><td><?= $customer->getPhoneNumber() ?></td>
        </tr>
      </table>
      <h3><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Order info') ?></h3>
      <table class="table table-bordered">
        <tr>
          <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Total product') ?></td><td><?= $cart->getTotalProducts() ?></td>
        </tr>
        <tr>
          <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Total price') ?></td><td><?= number_format($cart->getTotalPrice(), 2, '.', ' ') ?></td>
        </tr>
        <tr>
          <td><?= Yii::t(ShopModule::TRANSLATION_ORDER, 'Order time') ?></td><td><?= date('d-m-Y H:i', $order->firstStage->created_at) ?></td>
        </tr>
      </table>
        <?php $form = ActiveForm::begin(); ?>

          <?= $form->field($updateStatusForm, 'stage_id')
              ->dropDownList(
                  array_filter(
                      OrderStageLog::getStatuses(),
                      function ($key) use ($currentStage) {
                          return $key != $currentStage->stage_id;
                      },
                      ARRAY_FILTER_USE_KEY
                  )
              )->label('Status') ?>
          <?= $form->field($updateStatusForm, 'comment')->textarea() ?>

          <div class="form-group">
              <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
          </div>
        <?php ActiveForm::end(); ?>
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
        <?php foreach ($cart->cartProductSkus as $cartProductSku): ?>
        <?php
            $productSku = $cartProductSku->productSku;
            $currencySymbol = $productSku->currency->symbol;
            if ($productSku->isCurrencySet() && $productSku->currency->rate != 1.0) {
                $currencySymbol = 'грн.';
            }
          ?>
          <tr>
            <td><?= $productSku->id ?></td>
            <td>
            <?php if (! empty($productSku->getMainImage())): ?>
              <?= Html::img(
                  $fileWebHelper->getFileFullWebPath($productSku->getMainImage()),
                  ['style' => 'max-height: 150px;max-weight: 150px;']
                ) ?>
            <?php endif; ?>
            </td>
            <td>
                <?= Html::a(
                    $productSku->product->name . ' ' . $productSku->name,
                    Url::to(['product/update', 'id' => $productSku->product->id]),
                    ['target' => '_blank']
                    ) ?>
            </td>
            <td>
                <?= number_format($productSku->getCustomerPrice(), 2, '.', ' '); ?> <?= $currencySymbol ?>
            </td>
            <td><?= $cartProductSku->quantity ?></td>
            <td><?= number_format($cart->getTotalPrice(), 2, '.', ' ') ?> <?= $currencySymbol ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-9">
      <h3>Order history</h3>
      <table class="table table-condensed">
        <thead>
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Status</th>
            <th>User</th>
            <th>Comment</th>
          </tr>
        </thead>
        <?php foreach ($order->fullStageList as $key => $stage): ?>
        <tr>
          <td><?= $key ?></td>
          <td><?= date('d-m-Y H:i', $stage->created_at) ?></td>
          <td><?= OrderStageLog::getStatuses()[ $stage->stage_id ] ?></td>
          <td><?= isset($stage->user_id) ? $users[ $stage->user_id ]->username : ''; ?></td>
          <td><?= Html::encode($stage->comment) ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</div>
