<?php

/**
 * @var $buttons array
 */

?>

<nav>
  <ul class="pagination">
    <?php foreach ($buttons as $button): ?>
    <li class="<?= $button['active'] == true ? 'active' : '' ?> page-item">
      <?php if ($button['active']): ?>
      <span class="page-link"><?= $button['label'] ?></span>
      <?php else: ?>
      <a class="page-link" href="<?= $button['url'] ?>"><?= $button['label'] ?></a>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>
</nav>
