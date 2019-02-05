<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \shop\entities\search\ProductTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-type-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'product_title') ?>

    <?= $form->field($model, 'product_description') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('supplier', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('supplier', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
