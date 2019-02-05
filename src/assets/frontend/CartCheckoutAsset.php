<?php
namespace DmitriiKoziuk\yii2Shop\assets\frontend;

use yii\web\AssetBundle;

class CartCheckoutAsset extends AssetBundle
{
    public $sourcePath = '@DmitriiKoziuk/yii2Shop/web/frontend/cart/checkout';
    public $css = [
        'css/dk-shop-cart-checkout.css',
    ];
    public $js = [
    ];
    public $depends = [
        'DmitriiKoziuk\yii2Shop\assets\frontend\BaseThemeAsset',
    ];
}