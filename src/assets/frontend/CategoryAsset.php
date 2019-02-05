<?php
namespace DmitriiKoziuk\yii2Shop\assets\frontend;

use yii\web\AssetBundle;

class CategoryAsset extends AssetBundle
{
    public $sourcePath = '@DmitriiKoziuk/yii2Shop/web/frontend/category';
    public $css = [
        'css/shop-category.css',
    ];
    public $js = [
    ];
    public $depends = [
        'DmitriiKoziuk\yii2Shop\assets\frontend\BaseThemeAsset',
    ];
}