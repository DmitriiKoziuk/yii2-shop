<?php
namespace DmitriiKoziuk\yii2Shop\assets\backend;

use yii\web\AssetBundle;

class ProductAsset extends AssetBundle
{
    public $sourcePath = '@DmitriiKoziuk/yii2Shop/web/backend';
    public $css = [
        'css/product.css',
    ];
    public $js = [
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}