<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2Shop\ShopModule;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\Supplier */

$this->title = Yii::t(ShopModule::TRANSLATION_SUPPLIER, 'Create Supplier');
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_SUPPLIER, 'Suppliers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supplier-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
