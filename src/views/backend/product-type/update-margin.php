<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Base\BaseModule;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\entities\ProductTypeMargin;

/**
 * @var $this \yii\web\View
 * @var $updateData \DmitriiKoziuk\yii2Shop\data\ProductMarginUpdateData
 */

$this->title = Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE_MARGIN, 'Update margins for product type:') . ' ' . $updateData->getProductTypeData()->getName();
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Products'), 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Product types'), 'url' => ['product-type/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, $updateData->getProductTypeData()->getName()), 'url' => ['product-type/update', 'id' => $updateData->getProductTypeData()->getId()]];
$this->params['breadcrumbs'][] = Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE_MARGIN, 'Update product type currency margins');
?>

<div class="row">
  <div class="col-md-12">
    <?php $form = ActiveForm::begin(); ?>
      <table class="table table-bordered">
        <thead>
        <tr>
          <td>Currency name</td>
          <td>Margin type</td>
          <td>Margin value</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($updateData->getUpdateForms() as $key => $updateForm): ?>
          <?= $form->field($updateForm, "[{$key}]product_type_id")->textInput(['type' => 'hidden'])->label(false) ?>
          <?= $form->field($updateForm, "[{$key}]currency_id")->textInput(['type' => 'hidden'])->label(false) ?>
          <tr>
            <td><?= $updateData->getCurrencyNameById($updateForm->currency_id) ?></td>
            <td>
              <?= $form->field($updateForm, "[{$key}]margin_type")->dropDownList([
                  ProductTypeMargin::MARGIN_TYPE_SUM => 'Sum',
                  ProductTypeMargin::MARGIN_TYPE_PERCENT => 'Percent',
              ])->label(false) ?>
            </td>
            <td>
              <?= $form->field($updateForm, "[{$key}]margin_value")->textInput()->label(false) ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?= Html::submitButton(Yii::t(BaseModule::TRANSLATE, 'Update'), ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
  </div>
</div>
