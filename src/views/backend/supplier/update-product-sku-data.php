<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this \yii\web\View
 * @var $suppliersProductSkuData \DmitriiKoziuk\yii2Shop\data\SupplierProductSkuData[]
 * @var $allCurrencies \DmitriiKoziuk\yii2Shop\data\CurrencyData[]
 * @var $productSkuData \DmitriiKoziuk\yii2Shop\data\ProductSkuData
 */

$this->title = Yii::t(ShopModule::TRANSLATION_SUPPLIER, 'Update supplier product sku data:') . ' ' . $productSkuData->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Products'), 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT, $productSkuData->getFullName()), 'url' => ['product/update', 'id' => $productSkuData->getProductId()]];
$this->params['breadcrumbs'][] = 'Update suppliers product sku data';

$currencies = ArrayHelper::map($allCurrencies, 'id', 'name');
?>

<div class="row">
  <div class="col-md-12">
    <?php $form = ActiveForm::begin(); ?>
      <table class="table table-bordered">
        <thead>
        <tr>
          <td>Supplier name</td>
          <td>Supplier product unique id</td>
          <td>Product quantity</td>
          <td>Currency</td>
          <td>Purchase price</td>
          <td>Recommended price</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($suppliersProductSkuData as $key => $supplier): ?>
        <?php $updateForm = $supplier->getUpdateForm(); ?>
          <?= $form->field($updateForm, "[{$key}]supplier_id")->textInput(['type' => 'hidden'])->label(false) ?>
          <?= $form->field($updateForm, "[{$key}]product_sku_id")->textInput(['type' => 'hidden'])->label(false) ?>
          <tr>
            <td><?= $supplier->getSuppliedData()->getName() ?></td>
            <td><?= $form->field($updateForm, "[{$key}]supplier_product_unique_id")->textInput(['maxlength' => true])->label(false) ?></td>
            <td><?= $form->field($updateForm, "[{$key}]quantity")->textInput(['maxlength' => true])->label(false) ?></td>
            <td><?= $form->field($updateForm, "[{$key}]currency_id")->dropDownList($currencies)->label(false) ?></td>
            <td><?= $form->field($updateForm, "[{$key}]purchase_price")->textInput(['maxlength' => true])->label(false) ?></td>
            <td><?= $form->field($updateForm, "[{$key}]recommended_sell_price")->textInput(['maxlength' => true])->label(false) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
  </div>
</div>
