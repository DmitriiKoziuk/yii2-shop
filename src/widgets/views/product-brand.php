<?php

use yii\helpers\Url;
use yii\web\View;
use DmitriiKoziuk\yii2Shop\entities\Brand;

/**
 * @var $this View
 * @var $brands Brand[]
 * @var $indexPageUrl string
 */

?>

<h4>Brands</h4>
<ul>
    <?php foreach ($brands as $brand): ?>
    <?php $url = Url::to([
            '/customUrl/create',
            'url' => $indexPageUrl,
            'filterParams' => [
                'brand' => [
                    $brand->code,
                ],
            ]
        ]); ?>
    <li>
        <a href="<?= $url ?>"><?= $brand->name ?></a>
    </li>
    <?php endforeach; ?>
</ul>
