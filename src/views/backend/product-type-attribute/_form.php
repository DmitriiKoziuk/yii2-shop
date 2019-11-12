<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Shop\entities\ProductType;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;

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

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
