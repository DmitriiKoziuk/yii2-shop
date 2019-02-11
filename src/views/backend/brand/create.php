<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2Shop\ShopModule;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\Brand */

$this->title = Yii::t(ShopModule::TRANSLATION_BRAND, 'Create Brand');
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_BRAND, 'Brands'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
