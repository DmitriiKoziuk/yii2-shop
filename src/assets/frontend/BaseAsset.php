<?php
namespace DmitriiKoziuk\yii2Shop\assets\frontend;

use yii\web\AssetBundle;

class BaseAsset extends AssetBundle
{
    public static $defaultImageWebPath = '/images/default-image.jpg';

    public $sourcePath = '@DmitriiKoziuk/yii2Shop/web/frontend/base';
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}