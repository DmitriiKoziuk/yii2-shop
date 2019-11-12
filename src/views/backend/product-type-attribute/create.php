<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\ProductTypeAttributeEntity */

$this->title = 'Create Product Type Attribute Entity';
$this->params['breadcrumbs'][] = ['label' => 'Product Type Attribute Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-type-attribute-entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
