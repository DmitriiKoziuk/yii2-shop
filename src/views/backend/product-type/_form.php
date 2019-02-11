<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2Shop\entities\ProductType;

/**
 * @var $this                 \yii\web\View
 * @var $productType          \DmitriiKoziuk\yii2Shop\entities\ProductType
 * @var $productTypeInputForm \DmitriiKoziuk\yii2Shop\forms\product\ProductTypeInputForm
 */
?>

<div class="product-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($productTypeInputForm, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($productTypeInputForm, 'name_on_site')->textInput(['maxlength' => true]) ?>

    <?= $form->field($productTypeInputForm, 'product_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($productTypeInputForm, 'product_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($productTypeInputForm, 'product_url_prefix')->textInput(['maxlength' => true]) ?>

    <?= $form->field($productTypeInputForm, 'margin_strategy')->dropDownList([
        ProductType::MARGIN_STRATEGY_NOT_SET => 'Not set',
        ProductType::MARGIN_STRATEGY_USE_AVERAGE_SUPPLIER_PURCHASE_PRICE => 'From average supplier purchase price',
        ProductType::MARGIN_STRATEGY_USE_LOWER_SUPPLIER_PURCHASE_PRICE => 'From lower supplier purchase price',
        ProductType::MARGIN_STRATEGY_USE_HIGHEST_SUPPLIER_PURCHASE_PRICE => 'From highest supplier purchase price',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($productType->isNewRecord ? Yii::t(BaseModule::TRANSLATE, 'Create') : Yii::t('app', 'Update'), ['class' => $productType->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
