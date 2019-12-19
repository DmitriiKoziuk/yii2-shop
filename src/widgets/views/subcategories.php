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
    <?php foreach ($category->getDirectoryChildrenCategories() as $categoryEntity): ?>
    <li>
        <a href="<?= $categoryEntity->urlEntity->url ?>"><?= $categoryEntity->getFrontendName() ?></a>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
