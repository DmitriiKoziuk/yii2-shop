<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use shop\entities\ProductTypeMargin;
use shop\services\product\ProductTypeEntityService;

/**
 * @var $this        yii\web\View
 * @var $productType shop\entities\ProductType
 * @var $margins     shop\entities\ProductTypeMargin[]
 */

$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Products'), 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('product-type', 'Product types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('product-type', 'Update margin for') . " {$productType->name}";
?>

<div class="product-margin">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <?php $form = ActiveForm::begin() ?>
            <?php foreach ($margins as $key => $margin): ?>
                <?= $form->field($margin, "[{$key}]currency_id")->textInput([
                    'type' => 'hidden',
                ])->label(false) ?>
                <div class="row">
                    <div class="col-md-3">
                        <label>Currency code</label>
                        <?= Html::input('text', "[{$key}]currency_code", $margin->currency->code, [
                            'class' => 'form-control',
                            'disabled' => true
                        ]) ?>
                    </div>

                    <div class="col-md-3">
                        <?= $form->field($margin, "[{$key}]type")->dropDownList(
                            ProductTypeMargin::getTypes()
                        ) ?>
                    </div>

                    <div class="col-md-3">
                        <?= $form->field($margin, "[{$key}]value")->textInput() ?>
                    </div>

                    <div class="col-md-3">
                        <label>Products with this currency</label>
                        <?= Html::input(
                            'text',
                            "[{$key}]total_products",
                            ProductTypeEntityService::getMarginProductNumber($margin->product_type_id, $margin->currency_id),
                            [
                                'class' => 'form-control',
                                'disabled' => true
                            ]
                        ) ?>
                    </div>
                </div>

            <?php endforeach; ?>

            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>

            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
