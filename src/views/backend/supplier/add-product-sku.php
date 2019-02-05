<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this \yii\web\View
 * @var $suppliers \DmitriiKoziuk\yii2Shop\data\SupplierData[]
 */

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
