<?php

use DmitriiKoziuk\yii2Shop\assets\frontend\ProductWidgetAsset;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;

/**
 * @var $this \yii\web\View
 * @var $products \DmitriiKoziuk\yii2Shop\entities\Product[]
 * @var $productImages \DmitriiKoziuk\yii2FileManager\entities\File[]
 * @var $productMainImages \DmitriiKoziuk\yii2FileManager\entities\File[]
 * @var $pagination \yii\data\Pagination
 * @var $fileWebHelper FileWebHelper
 */

ProductWidgetAsset::register($this);
?>

<section class="products">
    <?php foreach ($products as $product): ?>
        <div class="product">
            <div class="image">
                <a href="<?= $product->getMainSku()->url ?>">
                  <?php if (! empty($productMainImages[ $product->getMainSku()->id ])): ?>
                    <img src="<?= $fileWebHelper->getFileFullWebPath(
                        $productMainImages[ $product->getMainSku()->id ]
                    ) ?>" alt="">
                  <?php endif; ?>
                </a>
            </div>
            <div class="title">
                <a href="<?= $product->getMainSku()->url ?>">
                    <span class="type-name"><?= $product->getTypeName() ?></span>
                    <?= $product->name ?> <?= $product->getMainSku()->name ?>
                </a>
            </div>
            <div class="price-scope">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="price">
                                <?= number_format(
                                    $product->getMainSku()->price_on_site,
                                    0,
                                    '.',
                                    ' '
                                ) ?>
                                <span class="currency">грн.</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <a class="btn buy-button" href="/cart/add-product?product=<?= $product->getMainSku()->id ?>">
                                <i class="fas fa-shopping-cart"></i>
                            </a>
                            <a class="btn favorite-button" href="/favorite/add?product=<?= $product->getMainSku()->id ?>">
                                <i class="far fa-heart"></i>
                            </a>
                            <a class="btn compare-button" href="/compare/add?product=<?= $product->getMainSku()->id ?>">
                                <i class="far fa-heart"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</section>
