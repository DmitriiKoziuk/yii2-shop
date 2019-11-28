<?php

/**
 * @var $buttons array
 */

?>

<?php foreach ($buttons as $button): ?>

  <?php if ($button['active']): ?>
  <span><?= $button['label'] ?></span>
  <?php else: ?>
  <a href="<?= $button['url'] ?>"><?= $button['label'] ?></a>
  <?php endif; ?>

<?php endforeach; ?>
