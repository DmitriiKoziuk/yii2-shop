<?php

/**
 * @var $buttons array
 */

?>

<nav>
  <ul class="pagination">
    <?php foreach ($buttons as $button): ?>
    <li class="<?= $button['active'] == true ? 'active' : '' ?>">
      <?php if ($button['active']): ?>
      <span><?= $button['label'] ?></span>
      <?php else: ?>
      <a href="<?= $button['url'] ?>"><?= $button['label'] ?></a>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>
</nav>
