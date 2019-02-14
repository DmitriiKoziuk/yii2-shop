<?php
namespace DmitriiKoziuk\yii2Shop\assets\frontend;

use yii\web\AssetBundle;

class ProductWidgetAsset extends AssetBundle
{
    public $sourcePath = '@DmitriiKoziuk/yii2Shop/web/frontend/product-widget';
    public $css = [
        'css/dk-shop-product-widget.css',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}