<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity */

$this->title = 'Create Eav Value Double Entity';
$this->params['breadcrumbs'][] = ['label' => 'Eav Value Double Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-value-double-entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
