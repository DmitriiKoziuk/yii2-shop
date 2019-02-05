<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this              yii\web\View
 * @var $categoryInputForm \DmitriiKoziuk\yii2Shop\forms\CategoryInputForm
 * @var $category          \DmitriiKoziuk\yii2Shop\entities\Category
 * @var $categories        \DmitriiKoziuk\yii2Shop\entities\Category[]
 */

$this->title = Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Create category');
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'categoryInputForm' => $categoryInputForm,
        'category'          => $category,
        'categories'        => $categories,
    ]) ?>

</div>
