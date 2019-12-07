<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Shop\entities\EavValueTypeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueTypeUnitEntity;
use DmitriiKoziuk\yii2Shop\forms\eav\EavAttributeCreateForm;

/* @var $this yii\web\View */
/* @var $model EavAttributeCreateForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="eav-attribute-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php if (! $model->isNewRecord): ?>

      <?= $form->field($model, 'name_for_product')->textInput(['maxlength' => true]) ?>

      <?= $form->field($model, 'name_for_filter')->textInput(['maxlength' => true]) ?>

      <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?php endif; ?>

    <?= $form->field($model, 'storage_type')->dropDownList(
        ['varchar' => 'Varchar', 'text' => 'Text', 'double' => 'Double', ],
        ['prompt' => '', 'disabled' => $model->isHasValues()]
    ) ?>

    <?= $form->field($model, 'selectable')->dropDownList([
        0 => 'No',
        1 => 'Yes',
    ]) ?>

    <?= $form->field($model, 'multiple')->dropDownList([
        0 => 'No',
        1 => 'Yes',
    ]) ?>

    <?php if (! $model->isNewRecord): ?>

      <?= $form->field($model, 'description_backend')->textarea(['maxlength' => true]) ?>

      <?= $form->field($model, 'description_frontend')->textarea(['maxlength' => true]) ?>

    <?php endif; ?>

    <?= $form->field($model, 'value_type_id')->dropDownList(ArrayHelper::map(
        EavValueTypeEntity::find()->all(),
        'id',
        'fullName'
    ), ['prompt' => ''])->label('Default value type') ?>

    <?php if (! $model->isNewRecord && ! empty($model->value_type_id)): ?>

      <?= $form->field($model, 'default_value_type_unit_id')->dropDownList(ArrayHelper::map(
          EavValueTypeUnitEntity::find()->where(['value_type_id' => $model->value_type_id])->all(),
          'id',
          'name'
      ), ['prompt' => ''])->label('Default value type unit') ?>

    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
