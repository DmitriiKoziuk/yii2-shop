<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Shop\entities\ProductType;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\ProductTypeAttributeEntity;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\ProductTypeAttributeEntity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-type-attribute-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'product_type_id')->dropDownList(ArrayHelper::map(
        ProductType::find()->all(),
        'id',
        'name'
    ))->label('Product type') ?>

    <?= $form->field($model, 'attribute_id')->dropDownList(ArrayHelper::map(
        EavAttributeEntity::find()->all(),
        'id',
        'name'
    ))->label('Attribute') ?>

    <?= $form->field($model, 'view_attribute_at_product_preview')->dropDownList([
        ProductTypeAttributeEntity::PREVIEW_NO => 'No',
        ProductTypeAttributeEntity::PREVIEW_YES => 'Yes',
    ])->label('Attribute') ?>

    <?php if (! $model->isNewRecord): ?>

    <?= $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>

    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
