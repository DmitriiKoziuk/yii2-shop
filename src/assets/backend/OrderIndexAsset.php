<?php
namespace DmitriiKoziuk\yii2Shop\assets\backend;

use yii\web\AssetBundle;

class OrderIndexAsset extends AssetBundle
{
    public $sourcePath = '@DmitriiKoziuk/yii2Shop/web/backend';
    public $css = [
        'css/order-index.css',
    ];
    public $js = [
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}