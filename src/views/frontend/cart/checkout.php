<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\assets\frontend\CartCheckoutAsset;

/**
 * @var $this \yii\web\View
 * @var $checkoutForm \DmitriiKoziuk\yii2Shop\forms\cart\CheckoutForm
 */
CartCheckoutAsset::register($this);
?>

<div class="container cart-checkout">
<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-3 label">
            <?= $checkoutForm->getAttributeLabel('phone_number') ?>
        </div>
        <div class="col-md-9">
            <?= $form->field($checkoutForm, 'phone_number')->label(false) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 label">
            <?= $checkoutForm->getAttributeLabel('first_name') ?>
        </div>
        <div class="col-md-9">
            <?= $form->field($checkoutForm, 'first_name')->label(false) ?>
        </div>
    </div>
    <?= Html::submitButton(Yii::t(ShopModule::TRANSLATION_CART, 'Checkout')) ?>
<?php ActiveForm::end(); ?>
</div>
