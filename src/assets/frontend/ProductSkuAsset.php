<?php
namespace DmitriiKoziuk\yii2Shop\assets\frontend;

use yii\web\AssetBundle;

class ProductSkuAsset extends AssetBundle
{
    public $sourcePath = '@DmitriiKoziuk/yii2Shop/web/frontend/product-sku';
    public $css = [
        'css/dk-shop-product-sku.css',
    ];
    public $js = [
    ];
    public $depends = [
        'DmitriiKoziuk\yii2Shop\assets\frontend\BaseThemeAsset',
    ];
}