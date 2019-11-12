<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\EavValueTypeUnitEntity */

$this->title = 'Create Eav Value Type Unit Entity';
$this->params['breadcrumbs'][] = ['label' => 'Eav Value Type Unit Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-value-type-unit-entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
