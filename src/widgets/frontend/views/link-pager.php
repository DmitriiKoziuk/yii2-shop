<?php

/**
 * @var $buttons array
 */

?>

<?php foreach ($buttons as $button): ?>

  <a href="<?= $button['url'] ?>"><?= $button['label'] ?></a>

<?php endforeach; ?>
