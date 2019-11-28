<?php

use yii\web\View;
use DmitriiKoziuk\yii2Shop\data\CategoryData;

/**
 * @var $this View
 * @var $category CategoryData
 */

?>

<?php if ($category->isHasChildrenCategories()): ?>
<h4>Categories</h4>
<ul>
    <?php foreach ($category->getChildrenCategories() as $categoryEntity) ?>
    <li>
        <a href="<?= $categoryEntity->url ?>"><?= $categoryEntity->getFrontendName() ?></a>
    </li>
    <?php ?>
</ul>
<?php endif; ?>
