<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

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

    <div class="form-group">
        <?= Html::submitButton($productType->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $productType->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
