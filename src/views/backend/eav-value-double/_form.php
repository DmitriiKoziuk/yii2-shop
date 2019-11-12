<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="eav-value-double-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'attribute_id')->dropDownList(ArrayHelper::map(
        EavAttributeEntity::find()->where([
            'storage_type' => EavAttributeEntity::STORAGE_TYPE_DOUBLE
        ])->all(),
        'id',
        'name'
    )) ?>

    <?= $form->field($model, 'value')->textInput() ?>

    <?php if (! $model->isNewRecord): ?>

        <?= $form->field($model, 'code')->textInput() ?>

    <?php endif; ?>

    <?php if(!$model->isNewRecord && ! is_null($model->attribute_id) && ! is_null($model->eavAttribute->valueType)): ?>

        <?= $form->field($model, 'value_type_unit_id')->dropDownList(ArrayHelper::map(
            $model->eavAttribute->valueType->units,
            'id',
            'name'
        ), ['prompt' => '']) ?>

    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
