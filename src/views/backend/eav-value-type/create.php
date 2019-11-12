<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\EavValueTypeEntity */

$this->title = 'Create Eav Value Type Entity';
$this->params['breadcrumbs'][] = ['label' => 'Eav Value Type Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-value-type-entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
