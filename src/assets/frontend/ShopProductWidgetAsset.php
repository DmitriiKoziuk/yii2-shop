<?php
namespace DmitriiKoziuk\yii2Shop\assets\frontend;

use yii\web\AssetBundle;

class ShopProductWidgetAsset extends AssetBundle
{
    public $sourcePath = '@DmitriiKoziuk/yii2Shop/web/frontend/product-widget';
    public $css = [
        'css/dk-shop-product-widget.css',
    ];
    public $js = [
    ];
    public $depends = [
        'DmitriiKoziuk\yii2Shop\assets\frontend\BaseAsset',
    ];
}