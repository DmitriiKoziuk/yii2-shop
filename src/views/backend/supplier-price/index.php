<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var $this yii\web\View
 * @var $searchModel DmitriiKoziuk\yii2Shop\entities\SupplierPriceSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $files \DmitriiKoziuk\yii2FileManager\entities\File[][]
 * @var $jobStatus array
 */

$this->title = Yii::t('app', 'Supplier Prices');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supplier-price-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'supplier_id',
            [
                'attribute' => 'Price name',
                'content' => function ($model) use ($files) {
                    /** @var \DmitriiKoziuk\yii2Shop\entities\SupplierPrice $model */
                    return !empty($files[ $model->id ]) ? (array_shift($files[ $model->id ]))->title : null;
                }
            ],
            'job_id',
            [
                'attribute' => 'Job status',
                'content' => function ($model) use ($jobStatus) {
                    /** @var \DmitriiKoziuk\yii2Shop\entities\SupplierPrice $model */
                    if (empty($jobStatus[ $model->id ])) {
                        return null;
                    } elseif (\yii\queue\Queue::STATUS_WAITING == $jobStatus[ $model->id ]) {
                        return 'Waiting';
                    } elseif (\yii\queue\Queue::STATUS_DONE == $jobStatus[ $model->id ]) {
                        return 'Done';
                    } elseif (\yii\queue\Queue::STATUS_RESERVED == $jobStatus[ $model->id ]) {
                        return 'In action';
                    }
                    return null;
                },
            ],
            'created_at:datetime',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{upload-price} {process-price}',
                'buttons' => [
                    'upload-price' => function ($url, $model) use ($files) {
                        return empty($files[ $model->id ]) ?
                            Html::a('<span class="glyphicon glyphicon-upload"></span>', $url) : null;
                    },
                    'process-price' => function ($url, $model) use ($files, $jobStatus) {
                        if (empty($jobStatus[ $model->id ])) {
                            /** @var \DmitriiKoziuk\yii2Shop\entities\SupplierPrice $model */
                            return !empty($files[ $model->id ]) ?
                                Html::a('<span class="glyphicon glyphicon-play"></span>', $url) : null;
                        }
                    },
                ],
            ],
        ],
    ]); ?>
</div>
