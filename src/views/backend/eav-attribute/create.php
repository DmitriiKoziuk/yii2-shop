<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Shop\entities\EavValueTypeEntity;
use DmitriiKoziuk\yii2Shop\forms\eav\EavAttributeCreateForm;

/* @var $this View */
/* @var $model EavAttributeCreateForm */
/* @var $form ActiveForm */

$this->title = 'Create Eav Attribute Entity';
$this->params['breadcrumbs'][] = ['label' => 'Eav Attribute Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-attribute-entity-create">

  <h1><?= Html::encode($this->title) ?></h1>

  <div class="eav-attribute-entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'storage_type')->dropDownList(
        ['varchar' => 'Varchar', 'text' => 'Text', 'double' => 'Double', ],
        ['prompt' => '']
    ) ?>

    <?= $form->field($model, 'selectable')->dropDownList([
        0 => 'No',
        1 => 'Yes',
    ]) ?>

    <?= $form->field($model, 'multiple')->dropDownList([
        0 => 'No',
        1 => 'Yes',
    ]) ?>

    <?= $form->field($model, 'view_at_frontend_faceted_navigation')->dropDownList([
        0 => 'No',
        1 => 'Yes',
    ]) ?>

    <?= $form->field($model, 'value_type_id')->dropDownList(ArrayHelper::map(
        EavValueTypeEntity::find()->all(),
        'id',
        'fullName'
    ), ['prompt' => ''])->label('Default value type') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

  </div>

</div>
