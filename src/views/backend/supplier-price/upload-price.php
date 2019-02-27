<?php

use DmitriiKoziuk\yii2FileManager\widgets\FileInputWidget;
use DmitriiKoziuk\yii2Shop\entities\SupplierPrice;

/**
 * @var $this \yii\web\View
 * @var $supplierPriceData \DmitriiKoziuk\yii2Shop\data\SupplierPriceData
 * @var $files \DmitriiKoziuk\yii2FileManager\entities\File
 * @var $supplier \DmitriiKoziuk\yii2Shop\data\SupplierData
 * @var $fileWebHelper \DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper
 */
?>

<h1>Upload supplier price</h1>

<div class="row">
  <div class="col-md-12">
    <?= FileInputWidget::widget([
        'entityName' => SupplierPrice::FILE_ENTITY_NAME,
        'entityId' => $supplierPriceData->getId(),
        'maxFileCount' => 1,
        'saveLocationAlias' => '@backend',
        'initialPreview' => $fileWebHelper
            ->getFileInputInitialPreview($files),
        'initialPreviewConfig' => $fileWebHelper
            ->getFileInputInitialPreviewConfig($files),
    ]) ?>
  </div>
</div>