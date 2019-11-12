<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this              yii\web\View
 * @var $currency          \DmitriiKoziuk\yii2Shop\entities\Currency
 * @var $currencyInputForm \DmitriiKoziuk\yii2Shop\forms\currency\CurrencyInputForm
 */

$this->title = Yii::t(ShopModule::TRANSLATION_CURRENCY, 'Update {modelClass}: ', [
    'modelClass' => 'Currency',
]) . $currency->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_CURRENCY, 'Currencies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $currency->name, 'url' => ['view', 'id' => $currency->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="currency-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'currency' => $currency,
        'currencyInputForm' => $currencyInputForm,
    ]) ?>

</div>
