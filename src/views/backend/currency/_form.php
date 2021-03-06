<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this              yii\web\View
 * @var $currency          \DmitriiKoziuk\yii2Shop\entities\Currency
 * @var $currencyInputForm \DmitriiKoziuk\yii2Shop\forms\currency\CurrencyInputForm
 */
?>

<div class="currency-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($currencyInputForm, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($currencyInputForm, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($currencyInputForm, 'symbol')->textInput(['maxlength' => true]) ?>

    <?= $form->field($currencyInputForm, 'rate')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($currency->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $currency->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
