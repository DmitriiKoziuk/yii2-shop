<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this                 \yii\web\View
 * @var $productType          \DmitriiKoziuk\yii2Shop\entities\ProductType
 * @var $productTypeInputForm \DmitriiKoziuk\yii2Shop\forms\product\ProductTypeInputForm
 */

$this->title = Yii::t('app', 'Update') .
    ' ' .
    Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'product type') .
    ': ' .
    $productType->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Product types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $productType->name, 'url' => ['view', 'id' => $productType->id]];
$this->params['breadcrumbs'][] = Yii::t(BaseModule::TRANSLATE, 'Update');
?>
<div class="product-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'productType' => $productType,
        'productTypeInputForm' => $productTypeInputForm,
    ]) ?>

</div>
