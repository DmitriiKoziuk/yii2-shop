<?php declare(strict_types=1);

use yii\web\View;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueTextEntity;

/**
 * @var $this View
 * @var $attributes EavAttributeEntity[]
 * @var $values array
 * @var $value EavValueDoubleEntity|EavValueVarcharEntity|EavValueTextEntity
 */

?>

<h3>Characteristics</h3>
<table class="table table-condensed">
<?php foreach ($attributes as $attribute): ?>
  <tr>
    <td><?= $attribute->name ?></td>
    <td>
    <?php foreach ($values[ $attribute->id ] as $value): ?>
      <?= $value->value ?> <?= $value->getUnitAbbreviation() ?>
    <?php endforeach; ?>
    </td>
  </tr>
<?php endforeach; ?>
</table>
