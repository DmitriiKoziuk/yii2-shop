<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="eav-value-varchar-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'attribute_id')->dropDownList(ArrayHelper::map(
        EavAttributeEntity::find()->where([
            'storage_type' => EavAttributeEntity::STORAGE_TYPE_VARCHAR
        ])->all(),
        'id',
        'name'
    ))->label('Attribute') ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?php if (! $model->isNewRecord): ?>
      <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
