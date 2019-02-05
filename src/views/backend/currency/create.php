<?php

use yii\helpers\Html;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * @var $this              yii\web\View
 * @var $currency          \DmitriiKoziuk\yii2Shop\entities\Currency
 * @var $currencyInputForm \DmitriiKoziuk\yii2Shop\forms\currency\CurrencyInputForm
 */

$this->title = Yii::t(ShopModule::TRANSLATION_CURRENCY, 'Create Currency');
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_CURRENCY, 'Currencies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'currency'          => $currency,
        'currencyInputForm' => $currencyInputForm,
    ]) ?>

</div>
