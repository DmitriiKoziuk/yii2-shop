<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use unclead\multipleinput\MultipleInput;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\forms\eav\EavVarcharSeoValuesCompositeForm;

/**
 * @var $this View
 * @var $valueEntity EavValueVarcharEntity
 * @var $seoValueForm EavVarcharSeoValuesCompositeForm
 */

?>

<h1><?= $valueEntity->eavAttribute->name ?>: <?= $valueEntity->value ?></h1>

<div class="eav-value-varchar-seo-values">
  <?php $form = ActiveForm::begin([
    'enableAjaxValidation'      => true,
    'enableClientValidation'    => false,
    'validateOnChange'          => false,
    'validateOnSubmit'          => true,
    'validateOnBlur'            => false,
  ]);?>

  <?= $form->field($seoValueForm, 'seoValues')->widget(MultipleInput::class, [
    'addButtonPosition' => MultipleInput::POS_FOOTER,
    'allowEmptyList' => true,
    'columns' => [
      [
        'name'  => 'code',
      ],
      [
        'name'  => 'value',
      ],
    ]
  ]);
  ?>

  <div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
  </div>

  <?php ActiveForm::end() ?>
</div>
