<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use DmitriiKoziuk\yii2Shop\entities\Product;

/**
 * @var $this View
 * @var $product Product
 */

?>

<h1>Sort product <?= $product->name ?></h1>

<div class="sort-form">
  <?= Html::beginForm(
        Url::to(['category-product/sort', 'id' => $product->id]),
        'post',
        [
            'class' => 'form-horizontal',
        ]
  ) ?>

  <?php foreach ($product->skus as $sku): ?>
  <h2><?= $sku->name ?></h2>
    <?php foreach ($sku->categoryProductSkuEntities as $categoryProductSkuEntity): ?>

    <div class="form-group">
      <label
        for="<?= "{$sku->id}-{$categoryProductSkuEntity->category_id}" ?>"
        class="col-sm-3 control-label"
      ><?= $categoryProductSkuEntity->category->name ?></label>
      <div class="col-sm-5">
        <div class="input-group">
          <?= Html::textInput(
              "productSkuList[{$sku->id}][{$categoryProductSkuEntity->category_id}]",
              $categoryProductSkuEntity->getSort(),
              [
                  'id' => "{$sku->id}-{$categoryProductSkuEntity->category_id}",
                  'class' => 'form-control',
              ]
          ) ?>
          <div class="input-group-addon"><?= $categoryProductSkuEntity->getMinSort() ?> - <?= $categoryProductSkuEntity->getMaxSort() ?></div>
        </div>
      </div>
    </div>

    <?php endforeach; ?>
  <?php endforeach; ?>

  <div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
  </div>

  <?= Html::endForm() ?>
</div>
