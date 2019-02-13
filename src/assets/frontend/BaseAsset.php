<?php
namespace DmitriiKoziuk\yii2Shop\assets\frontend;

use yii\web\AssetBundle;

class BaseAsset extends AssetBundle
{
    public $sourcePath = '@DmitriiKoziuk/yii2Shop/web/frontend/base';
    public $css = [
        'css/bootstrap-grid.css',
        'css/dk-shop-base.css',
    ];
    public $js = [
        'js/dk-shop-base.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}