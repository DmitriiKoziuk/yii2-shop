<?php

use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this \yii\web\View
 */
$this->title = Yii::t(ShopModule::TRANSLATION_CART, 'Cart is empty');
$this->registerMetaTag([
    'name' => 'description',
    'content' => Yii::t(ShopModule::TRANSLATION_CART, 'Cart is empty')
]);
?>

<div class="cart-empty">
  <?= Yii::t(ShopModule::TRANSLATION_CART, 'Cart is empty') ?>
</div>
