<?php

return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../../../../../common/config/main.php',
    require __DIR__ . '/../../../../../../common/config/main-local.php',
    require __DIR__ . '/../../../../../../common/config/test-local.php',
    require __DIR__ . '/../../../../../../frontend/config/main.php',
    require __DIR__ . '/../../../../../../frontend/config/main-local.php',
    require __DIR__ . '/../../../../../../frontend/config/test.php',
    require __DIR__ . '/test.php'
);