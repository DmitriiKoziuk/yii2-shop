<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\search\ProductTypeAttributeEntitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-type-attribute-entity-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'product_type_id') ?>

    <?= $form->field($model, 'attribute_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
