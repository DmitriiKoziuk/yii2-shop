<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this \yii\web\View
 * @var $checkoutForm \DmitriiKoziuk\yii2Shop\forms\cart\CheckoutForm
 */

$this->title = Yii::t(ShopModule::TRANSLATION_CART, 'Checkout');
$this->registerMetaTag([
    'name' => 'description',
    'content' => Yii::t(ShopModule::TRANSLATION_CART, 'Checkout')
]);
?>

<div class="cart-checkout">
<?php $form = ActiveForm::begin(); ?>
  <div class="row">
    <div class="col-md-3">
      <?= $checkoutForm->getAttributeLabel('phone_number') ?>
    </div>
    <div class="col-md-9">
      <?= $form->field($checkoutForm, 'phone_number')->label(false) ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3">
      <?= $checkoutForm->getAttributeLabel('first_name') ?>
    </div>
    <div class="col-md-9">
      <?= $form->field($checkoutForm, 'first_name')->label(false) ?>
    </div>
  </div>
  <?= Html::submitButton(Yii::t(ShopModule::TRANSLATION_CART, 'Checkout')) ?>
<?php ActiveForm::end(); ?>
</div>
