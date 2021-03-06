<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2FileManager\widgets\FileInputWidget;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\Currency;
use DmitriiKoziuk\yii2Shop\forms\product\ProductSkuUpdateForm;
use DmitriiKoziuk\yii2Shop\assets\backend\ProductAsset;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2Shop\widgets\backend\ProductSkuUpdateAttributesWidget;

/**
 * @var $this \yii\web\View
 * @var $form \yii\widgets\ActiveForm
 * @var $currencyList Currency[]
 * @var $product Product
 * @var $productSkuUpdateForms ProductSkuUpdateForm[]
 * @var $productSkusSuppliers \DmitriiKoziuk\yii2Shop\data\SupplierProductSkuData[][]
 * @var $fileWebHelper FileWebHelper
 * @var $productSkuViewHelper \DmitriiKoziuk\yii2Shop\helpers\ProductSkuViewHelper
 */

$this->registerAssetBundle(ProductAsset::class);
?>

<div class="product-sku">
    <div class="head">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <span class="title">Sku name</span>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-2">
                                      <span class="title">Stock</span>
                                    </div>
                                    <div class="col-md-2">
                                      <span class="title">Price strategy</span>
                                    </div>
                                    <div class="col-md-2">
                                      <span class="title">Currency</span>
                                    </div>
                                    <div class="col-md-2">
                                      <span class="title">Sell price</span>
                                    </div>
                                    <div class="col-md-2">
                                      <span class="title">Old price</span>
                                    </div>
                                    <div class="col-md-2">
                                      <span class="title">Price on site</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php foreach ($productSkuUpdateForms as $key => $productSkuUpdateForm): ?>
        <?= $form->field($productSkuUpdateForm, "[{$key}]id")->textInput(['type' => 'hidden'])->label(false) ?>
        <div class="row">
            <div class="col-md-12">
                <div class="product-sku-item">
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($productSkuUpdateForm, "[{$key}]name")
                                ->textInput(['maxlength' => true])
                                ->label(false);
                            ?>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <?= $form->field($productSkuUpdateForm, "[{$key}]stock_status")
                                                ->dropDownList(ProductSku::getStockVariation())
                                                ->label(false);
                                            ?>
                                        </div>
                                        <div class="col-md-2">
                                            <?= $form->field($productSkuUpdateForm, "[{$key}]sell_price_strategy")
                                                ->dropDownList(ProductSku::getSellPriceStrategyVariation())
                                                ->label(false);
                                            ?>
                                        </div>
                                        <div class="col-md-2">
                                            <?= $form->field($productSkuUpdateForm, "[{$key}]currency_id")->dropDownList(
                                                ArrayHelper::map($currencyList, 'id', 'name'),
                                                [
                                                    'prompt' => 'Select currency',
                                                ]
                                            )->label(false); ?>
                                        </div>
                                        <div class="col-md-2">
                                            <?= $form->field($productSkuUpdateForm, "[{$key}]sell_price")
                                                ->textInput([
                                                    'maxlength' => true,
                                                    'value' => $productSkuViewHelper->priceFormat($productSkuUpdateForm->sell_price),
                                                ])->label(false);
                                            ?>
                                        </div>
                                        <div class="col-md-2">
                                            <?= $form->field($productSkuUpdateForm, "[{$key}]old_price")
                                                ->textInput([
                                                    'maxlength' => true,
                                                    'value' => $productSkuViewHelper->priceFormat($productSkuUpdateForm->old_price),
                                                ])
                                                ->label(false);
                                            ?>
                                        </div>
                                        <div class="col-md-2">
                                            <?= $form->field($productSkuUpdateForm, "[{$key}]customer_price")
                                                ->textInput([
                                                    'maxlength' => true,
                                                    'disabled' => true,
                                                    'value' => $productSkuViewHelper->priceFormat($productSkuUpdateForm->customer_price),
                                                ])
                                                ->label(false);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button
                                        class="btn btn-default"
                                        type="button"
                                        data-toggle="collapse"
                                        data-target="#collapseImages-<?=$key?>"
                                        aria-expanded="false"
                                        aria-controls="collapseImages"
                                    >
                                        <span class="glyphicon glyphicon-picture" aria-hidden="true"></span>
                                    </button>
                                    <button
                                        class="btn btn-default"
                                        type="button"
                                        data-toggle="collapse"
                                        data-target="#collapseSeo-<?=$key?>"
                                        aria-expanded="false"
                                        aria-controls="collapseSeo"
                                    >
                                        <span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
                                    </button>
                                  <button
                                      class="btn btn-default"
                                      type="button"
                                      data-toggle="collapse"
                                      data-target="#collapseSuppliers-<?=$key?>"
                                      aria-expanded="false"
                                      aria-controls="collapseSuppliers"
                                  >
                                    <span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span>
                                  </button>
                                  <button
                                      class="btn btn-default"
                                      type="button"
                                      data-toggle="collapse"
                                      data-target="#collapseAttributes-<?=$key?>"
                                      aria-expanded="false"
                                      aria-controls="collapseAttributes"
                                  >
                                    <span class="glyphicon glyphicon glyphicon-th-list" aria-hidden="true"></span>
                                  </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="collapse" id="collapseImages-<?=$key?>">
                    <div class="collapse-inside">
                        <div class="row">
                            <div class="col-md-12">
                                <?= FileInputWidget::widget([
                                    'entityName' => ProductSku::FILE_ENTITY_NAME,
                                    'entityId' => $productSkuUpdateForm->id,
                                    'fileName' => $product->slug . '-' . $productSkuUpdateForm->slug,
                                    'initialPreview' => $fileWebHelper
                                        ->getFileInputInitialPreview($productSkuUpdateForm->files),
                                    'initialPreviewConfig' => $fileWebHelper
                                        ->getFileInputInitialPreviewConfig($productSkuUpdateForm->files),
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="collapseSeo-<?=$key?>">
                    <div class="collapse-inside">
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($productSkuUpdateForm, "[{$key}]slug")
                                    ->textInput(['maxlength' => true])
                                    ->label('Slug');
                                ?>
                            </div>
                            <div class="col-md-8">
                                <?= $form->field($productSkuUpdateForm, "[{$key}]url")
                                    ->textInput(['maxlength' => true, 'disabled' => true,])
                                    ->label('Url');
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?= $form->field($productSkuUpdateForm, "[{$key}]meta_title")
                                    ->textInput(['maxlength' => true]);
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?= $form->field($productSkuUpdateForm, "[{$key}]meta_description")
                                    ->textarea();
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?= $form->field($productSkuUpdateForm, "[{$key}]short_description")
                                    ->textarea();
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?= $form->field($productSkuUpdateForm, "[{$key}]description")
                                    ->textarea();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="collapseSuppliers-<?=$key?>">
                  <div class="collapse-inside">
                    <div class="row">
                      <div class="col-md-12">
                        <table class="table table-bordered">
                          <caption>Suppliers data</caption>
                          <thead>
                            <tr>
                              <td>Name</td>
                              <td>Phone number</td>
                              <td>Unique id</td>
                              <td>Quantity</td>
                              <td>Currency</td>
                              <td>Purchase price</td>
                              <td>Recommended price</td>
                              <td>Recommended profit</td>
                              <td>Actual profit</td>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (isset($productSkusSuppliers[ $productSkuUpdateForm->id ])): ?>
                            <?php foreach ($productSkusSuppliers[ $productSkuUpdateForm->id ] as $supplier): ?>
                            <tr>
                              <td><?= $supplier->getSuppliedData()->getName() ?></td>
                              <td><?= $supplier->getSuppliedData()->getPhoneNumber() ?></td>
                              <td><?= $supplier->getUniqueId() ?></td>
                              <td><?= $supplier->getQuantity() ?></td>
                              <td><?= $supplier->getCurrencyName() ?></td>
                              <td><?= $supplier->getPurchasePrice() ?></td>
                              <td><?= $supplier->getRecommendedSellPrice() ?></td>
                              <td><?= $supplier->getRecommendedProfit() ?></td>
                              <td><?= $supplier->getActualProfit($productSkuUpdateForm->sell_price) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                          </tbody>
                        </table>
                        <?= Html::a(
                            'Add supplier to this product sku',
                            Url::to([
                                'supplier/add-product-sku',
                                'product_sku_id' => $productSkuUpdateForm->id,
                            ]),
                            ['class' => 'btn btn-default']
                        ) ?>
                          <?= Html::a(
                              'Update supplier data',
                              Url::to([
                                  'supplier/update-product-sku-data',
                                  'product_sku_id' => $productSkuUpdateForm->id,
                              ]),
                              ['class' => 'btn btn-default']
                          ) ?>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="collapse" id="collapseAttributes-<?=$key?>">
                  <div class="collapse-inside">
                    <?= ProductSkuUpdateAttributesWidget::widget([
                        'productSkuId' => $productSkuUpdateForm->id,
                    ]) ?>
                  </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
