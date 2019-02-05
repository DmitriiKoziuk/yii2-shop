<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\helpers\CategoryHelper;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this yii\web\View
 * @var $product Product
 * @var $categories \DmitriiKoziuk\yii2Shop\entities\Category[]
 * @var $productTypes[] ProductType
 * @var $currencyList \DmitriiKoziuk\yii2Shop\entities\Currency[]
 * @var $productInputForm \DmitriiKoziuk\yii2Shop\forms\product\ProductInputForm
 * @var $productSkuInputForms \DmitriiKoziuk\yii2Shop\forms\product\ProductSkuInputForm[]
 * @var $fileWebHelper \DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper
 */

$this->title = Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Update Product:') . ' ' . $product->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $product->name;
?>
<div class="product-update">
<?php $form = ActiveForm::begin(); ?>
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-3">
          <?= $form->field($productInputForm, 'category_id')->widget(
              Select2::class,
              [
                  'data' => ArrayHelper::map(
                      CategoryHelper::categoryTreeToList(
                          CategoryHelper::createCategoryTree($categories)
                      ),
                      'id',
                      'name'
                  ),
                  'options' => [
                      'placeholder' => 'Select a category ...',
                  ],
                  'pluginOptions' => [
                      'allowClear' => true
                  ],
              ]
          )->label('Product category'); ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($productInputForm, 'type_id')->widget(
              Select2::class,
              [
                  'data' => ArrayHelper::map($productTypes, 'id', 'name'),
                  'options' => [
                      'placeholder' => 'Select a product type ...',
                  ],
                  'pluginOptions' => [
                      'allowClear' => true
                  ],
              ]
          )->label('Product type'); ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($productInputForm, 'name')
              ->textInput(['maxlength' => true])
              ->label('Product name');
          ?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
            <?= $form->field($productInputForm, 'slug')
                ->textInput(['maxlength' => true])
                ->label('Product slug');
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($productInputForm, 'url')
                ->textInput(['maxlength' => true])
                ->label('Product url');
            ?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <h4 class="text-center">Product skus</h4>
        </div>
      </div>

      <?= $this->render('_sku-update', [
          'form' => $form,
          'currencyList' => $currencyList ,
          'product' => $product,
          'productSkuInputForms' => $productSkuInputForms,
          'fileWebHelper' => $fileWebHelper,
      ]) ?>

    </div>
  </div>
  <div class="row" style="margin-top: 15px;">
    <div class="col-md-12">
      <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(
            Yii::t('app', 'Create sku'),
            ['product/create-sku', 'product_id' => $product->id],
            ['class' => 'btn btn-success']
        ) ?>
      </div>
    </div>
  </div>
<?php ActiveForm::end(); ?>
</div>
