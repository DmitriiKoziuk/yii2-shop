<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\ProductTypeAttributeEntity */

$this->title = 'Update Product Type Attribute Entity: ' . $model->product_type_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Type Attribute Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->product_type_id, 'url' => ['view', 'product_type_id' => $model->product_type_id, 'attribute_id' => $model->attribute_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-type-attribute-entity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
