<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Shop\entities\EavValueTypeEntity;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\EavValueTypeUnitEntity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="eav-value-type-unit-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'value_type_id')->dropDownList(ArrayHelper::map(
        EavValueTypeEntity::find()->all(),
        'id',
        'name'
    )) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'abbreviation')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
