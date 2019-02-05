<?php

/**
 * @var $this yii\web\View
 * @var $tree array
 */

?>

<div class="full-category-tree">
    <div class="row">
    <?php foreach ($tree as $value): ?>
        <div class="col-md-8 col-md-offset-2"><?= $value['name'] ?></div>
    <?php endforeach; ?>
    </div>
</div>
