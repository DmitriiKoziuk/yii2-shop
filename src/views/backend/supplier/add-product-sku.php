<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this \yii\web\View
 * @var $suppliers \DmitriiKoziuk\yii2Shop\data\SupplierData[]
 * @var $productSkuData \DmitriiKoziuk\yii2Shop\data\ProductSkuData
 */

$this->title = Yii::t(ShopModule::TRANSLATION_SUPPLIER, 'Add suppliers to product sku data:') . ' ' . $productSkuData->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Products'), 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT, $productSkuData->getFullName()), 'url' => ['product/update', 'id' => $productSkuData->getProductId()]];
$this->params['breadcrumbs'][] = 'Add suppliers';
?>

<div class="row">
  <div class="col-md-12">
    <?php $form = ActiveForm::begin(); ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <td></td>
            <td>Supplier name</td>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($suppliers as $supplier): ?>
          <tr>
            <td>
              <?= Html::checkbox("selected[{$supplier->getId()}]") ?>
            </td>
            <td>
              <?= $supplier->getName() ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?= Html::submitButton('Save', ['class' => 'btn btn-success']); ?>
    <?php ActiveForm::end() ?>
  </div>
</div>
