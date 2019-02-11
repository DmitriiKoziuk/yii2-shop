<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this              yii\web\View
 * @var $categoryInputForm \DmitriiKoziuk\yii2Shop\forms\CategoryInputForm
 * @var $category          \DmitriiKoziuk\yii2Shop\entities\Category
 * @var $categories        \DmitriiKoziuk\yii2Shop\entities\Category[]
 */

$this->title = Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Update {modelClass}: ', [
    'modelClass' => 'Category',
]) . $category->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $category->name, 'url' => ['view', 'id' => $category->id]];
$this->params['breadcrumbs'][] = Yii::t(BaseModule::TRANSLATE, 'Update');
?>
<div class="category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'categoryInputForm' => $categoryInputForm,
        'category'          => $category,
        'categories'        => $categories,
    ]) ?>

</div>
