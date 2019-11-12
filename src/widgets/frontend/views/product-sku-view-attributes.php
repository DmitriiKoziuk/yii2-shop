<?php declare(strict_types=1);

use yii\web\View;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;

/**
 * @var $this View
 * @var int $productSkuId
 * @var $attributes EavAttributeEntity[]
 * @var $attributeValues EavValueDoubleEntity[]|EavValueVarcharEntity[]
 */

?>

<table class="table table-condensed">
<?php foreach ($attributes as $attribute): ?>
  <tr>
    <td><?= $attribute->name ?></td>
    <td>
    <?php foreach ($attributeValues[ $attribute->id ] as $value): ?>
      <?= $value->value ?>
    <?php endforeach; ?>
    </td>
  </tr>
<?php endforeach; ?>
</table>
