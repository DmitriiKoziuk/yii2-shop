<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this                \yii\web\View
 * @var $productInputForm    \DmitriiKoziuk\yii2Shop\forms\product\ProductInputForm
 * @var $productSkuInputForm \DmitriiKoziuk\yii2Shop\forms\product\ProductSkuInputForm
 */

$this->title = Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Create Product');
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="product-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
          <div class="col-md-6">
              <?= $form->field($productInputForm, 'name')
                  ->textInput(['maxlength' => true])
                  ->label('Product name');
              ?>
          </div>
          <div class="col-md-6">
              <?= $form->field($productSkuInputForm, 'name')
                  ->textInput(['maxlength' => true])
                  ->label('Sku name');
              ?>
          </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t(BaseModule::TRANSLATE, 'Create'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
