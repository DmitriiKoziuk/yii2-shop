<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this                 \yii\web\View
 * @var $productType          \DmitriiKoziuk\yii2Shop\entities\ProductType
 * @var $productTypeInputForm \DmitriiKoziuk\yii2Shop\forms\product\ProductTypeInputForm
 */

$this->title = Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Create product type');
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Product types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'productType' => $productType,
        'productTypeInputForm' => $productTypeInputForm,
    ]) ?>

</div>
