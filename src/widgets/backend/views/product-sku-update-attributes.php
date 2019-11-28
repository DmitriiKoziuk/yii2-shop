<?php declare(strict_types=1);

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;

/**
 * @var $this View
 * @var int $productSkuId
 * @var $attributes EavAttributeEntity[]
 * @var $attributeValues array
 */

?>

<div class="row">
  <div class="col-md-12">
    <div class="form-horizontal">
    <?php foreach ($attributes as $attribute): ?>
      <div class="form-group">
        <?= Html::label($attribute->name, "attribute-{$attribute->id}", [
            'class' => 'col-md-2 control-label',
        ]) ?>
        <div class="col-md-10">
          <?php if (! $attribute->selectable && EavAttributeEntity::STORAGE_TYPE_TEXT !== $attribute->storage_type): ?>
            <?php
              /** @var EavValueDoubleEntity|EavValueVarcharEntity $value */
              $value = empty($attributeValues[ $attribute->id ]) ? null : array_shift($attributeValues[ $attribute->id ])
            ?>
            <div class="row">
              <div class="col-md-8">
                  <?= Html::input(
                      'text',
                      "productSku[$productSkuId][{$attribute->id}][1][value]",
                      empty($value->value) ? null : $value->value,
                      [
                        'class' => 'form-control',
                        'id' => "attribute-{$attribute->id}",
                      ]
                  ) ?>
              </div>
              <div class="col-md-4">
                <?php if (! is_null($attribute->valueType) && ! empty($attribute->valueType->units)): ?>
                  <?= Html::dropDownList(
                      "productSku[$productSkuId][{$attribute->id}][1][unit_id]",
                      empty($value) ? null : $value->value_type_unit_id,
                      ArrayHelper::map($attribute->valueType->units, 'id', 'fullName'),
                      [
                          'class' => 'form-control',
                      ]
                  ) ?>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
          <?php if (! $attribute->selectable && EavAttributeEntity::STORAGE_TYPE_TEXT === $attribute->storage_type): ?>
              <div class="row">
                <div class="col-md-8">
                    <?= Html::textarea(
                        "productSku[$productSkuId][{$attribute->id}][1][value]",
                        null,
                        [
                            'class' => 'form-control',
                            'id' => "attribute-{$attribute->id}",
                        ]
                    ) ?>
                </div>
                <div class="col-md-4">
                  <?php if (! is_null($attribute->valueType) && ! empty($attribute->valueType->units)): ?>
                    <?= Html::dropDownList(
                        "productSku[$productSkuId][{$attribute->id}][unit_id]",
                        [],
                        ArrayHelper::map($attribute->valueType->units, 'id', 'name'),
                        [
                            'class' => 'form-control',
                        ]
                    ) ?>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>
          <?php if ($attribute->selectable && ! $attribute->multiple): ?>
            <?php $value = empty($attributeValues[ $attribute->id ]) ? null : array_shift($attributeValues[ $attribute->id ]) ?>
            <div class="row">
              <div class="col-md-8">
                <?= Html::dropDownList(
                    "productSku[$productSkuId][{$attribute->id}][1][value]",
                    empty($value) ? null : $value->id,
                    ArrayHelper::map($attribute->selectableValues, 'id', 'value'),
                    [
                        'prompt' => '',
                        'class' => 'form-control',
                    ]
                ) ?>
              </div>
              <div class="col-md-4">
                <?php if (! is_null($attribute->valueType) && ! empty($attribute->valueType->units)): ?>
                  <?= Html::dropDownList(
                      "productSku[$productSkuId][{$attribute->id}][1][unit_id]",
                        empty($value->unit) ? $attribute->default_value_type_unit_id : $value->unit->id,
                      ArrayHelper::map($attribute->valueType->units, 'id', 'name'),
                      [
                          'class' => 'form-control',
                      ]
                  ) ?>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
            <?php if ($attribute->selectable && $attribute->multiple): ?>
              <div class="row">
                <div class="col-md-8">
                    <?= Html::checkboxList(
                        "productSku[$productSkuId][{$attribute->id}]",
                        empty($attributeValues[ $attribute->id ]) ? null : array_values(ArrayHelper::map($attributeValues[ $attribute->id ], 'id', 'id')),
                        ArrayHelper::map($attribute->selectableValues, 'id', 'value'),
                        [
                            'multiple'=>'multiple',
                            'class' => 'checkbox',
                        ]
                    ) ?>
                </div>
              </div>
            <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</div>
