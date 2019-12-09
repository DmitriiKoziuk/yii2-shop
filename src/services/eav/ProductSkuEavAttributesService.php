<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\services\eav;

use Exception;
use yii\db\Expression;
use yii\helpers\Inflector;
use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Base\exceptions\ExternalComponentException;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\ProductTypeAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueTextEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleProductSkuEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueTextProductSkuEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharProductSkuEntity;

class ProductSkuEavAttributesService extends DBActionService
{
    /**
     * @param array $updatedProductSkuData
     * @param int $productTypeId
     * @throws ExternalComponentException
     * @throws \Throwable
     */
    public function update(array $updatedProductSkuData, int $productTypeId): void
    {
        try {
            $this->beginTransaction();
            $productTypeAttributes = $this->getProductTypeAttributes($productTypeId);
            foreach ($updatedProductSkuData as $productSkuId => $updatedAttributes) {
                $productSkuValues = $this->getProductSkuValues($productSkuId);
                foreach ($updatedAttributes as $attributeId => $values) {
                    if (array_key_exists($attributeId, $productTypeAttributes)) {
                        $attribute = $productTypeAttributes[ $attributeId ];
                        $this->updateValues(
                            $productSkuId,
                            $attribute,
                            $values,
                            $productSkuValues
                        );
                    } else {
                        throw new Exception('Try update not exist attribute values.');
                    }
                }
            }
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param int $productSkuId
     * @param EavAttributeEntity $attribute
     * @param array $values
     * @param array $productSkuValues
     * @throws Exception
     * @throws \Throwable
     */
    private function updateValues(
        int $productSkuId,
        EavAttributeEntity $attribute,
        array $values,
        array $productSkuValues
    ) {
        if (! $attribute->selectable) {
            $this->updateNonSelectableSingleValue(
                $attribute,
                $productSkuId,
                $values,
                $productSkuValues
            );
        } elseif ($attribute->selectable && ! $attribute->multiple) {
            $this->updateSelectableValues(
                $attribute,
                $productSkuId,
                $values,
                $productSkuValues
            );
        } elseif ($attribute->selectable && $attribute->multiple) {
            $this->updateMultiplyValues(
                $attribute,
                $productSkuId,
                $values,
                $productSkuValues
            );
        }
    }

    /**
     * @param EavAttributeEntity $attribute
     * @param int $productSkuId
     * @param array $values
     * @param array $productSkuValues
     * @throws Exception
     * @throws \Throwable
     */
    private function updateNonSelectableSingleValue(
        EavAttributeEntity $attribute,
        int $productSkuId,
        array $values,
        array $productSkuValues
    ) {
        $value = array_shift($values);
        $oldValue = null;
        if (! empty($productSkuValues[ $attribute->id ])) {
            $oldValue = array_shift($productSkuValues[ $attribute->id ]);
        }

        if (! empty($value['value'])) {
            switch ($attribute->storage_type) {
                case EavAttributeEntity::STORAGE_TYPE_DOUBLE:
                    $this->updateDoubleValue(
                        $productSkuId,
                        (float) $value['value'],
                        $attribute,
                        $oldValue,
                        isset($value['unit_id']) ? (int) $value['unit_id'] : null
                    );
                    break;
                case EavAttributeEntity::STORAGE_TYPE_VARCHAR:
                    $this->updateVarcharValue(
                        $productSkuId,
                        $value['value'],
                        $attribute,
                        $oldValue
                    );
                    break;
                case EavAttributeEntity::STORAGE_TYPE_TEXT:
                    $this->updateTextValue(
                        $productSkuId,
                        $value['value'],
                        $attribute,
                        $oldValue
                    );
                    break;
                default:
                    throw new Exception('Non exist storage type.');
            }
        }

        if (empty($value['value']) && ! empty($oldValue)) {
            $this->deleteRelation($attribute, $oldValue->id, $productSkuId);
            if ($attribute->storage_type === EavAttributeEntity::STORAGE_TYPE_TEXT) {
                $this->deleteValue($oldValue);
            }
        }
    }

    /**
     * @param EavAttributeEntity $attribute
     * @param int $productSkuId
     * @param array $values
     * @param array $productSkuValues
     * @throws Exception
     * @throws \Throwable
     */
    private function updateSelectableValues(
        EavAttributeEntity $attribute,
        int $productSkuId,
        array $values,
        array $productSkuValues
    ) {
        $valueId = (array_shift($values))['value'];

        // delete exist relation if value empty
        if (
            array_key_exists($attribute->id, $productSkuValues) &&
            empty($valueId)
        ) {
            $oldValueId = (array_shift($productSkuValues[ $attribute->id ]))->id;
            $this->deleteRelation(
                $attribute,
                $oldValueId,
                $productSkuId
            );
            unset($productSkuValues[ $attribute->id ]);
        }

        // delete exist relation if selected new value
        if (
            array_key_exists($attribute->id, $productSkuValues) &&
            ! array_key_exists($valueId, $productSkuValues[ $attribute->id ])
        ) {
            $oldValueId = (array_shift($productSkuValues[ $attribute->id ]))->id;
            $this->deleteRelation(
                $attribute,
                $oldValueId,
                $productSkuId
            );
            unset($productSkuValues[ $attribute->id ]);
        }

        // add relation
        if (
            ! array_key_exists($attribute->id, $productSkuValues) &&
            ! empty($valueId)
        ) {
            $this->createRelation(
                $attribute->storage_type,
                $productSkuId,
                (int) $valueId
            );
        }
    }

    /**
     * @param EavAttributeEntity $attribute
     * @param int $productSkuId
     * @param array $newValues
     * @param array $productSkuValues
     * @throws \Throwable
     */
    private function updateMultiplyValues(
        EavAttributeEntity $attribute,
        int $productSkuId,
        array $newValues,
        array $productSkuValues
    ) {
        if (array_key_exists($attribute->id, $productSkuValues)) {
            $productSkuValues = $productSkuValues[ $attribute->id ];

            // delete all relation
            if (empty($newValues)) {
                foreach ($productSkuValues as $productSkuValue) {
                    $this->deleteRelation(
                        $attribute,
                        $productSkuValue->id,
                        $productSkuId
                    );
                }
            } else {
                foreach ($productSkuValues as $productSkuValue) {
                    if (false === array_search($productSkuValue->id, $newValues)) {
                        $this->deleteRelation(
                            $attribute,
                            $productSkuValue->id,
                            $productSkuId
                        );
                        unset($productSkuValues[ $productSkuValue->id ]);
                    }
                }

                foreach ($newValues as $key => $valueId) {
                    if (! array_key_exists($valueId, $productSkuValues)) {
                        $this->createRelation(
                            $attribute->storage_type,
                            $productSkuId,
                            (int) $valueId
                        );
                    }
                    unset($newValues[ $key ]);
                }
            }
        }

        if (! empty($newValues)) {
            foreach ($newValues as $key => $valueId) {
                $this->createRelation(
                    $attribute->storage_type,
                    $productSkuId,
                    (int) $valueId
                );
            }
        }
    }

    private function getProductSkuValues(int $productSkuId): array
    {
        $productSku = ProductSku::find()
            ->with([
                'eavVarcharValues.eavAttribute',
                'eavTextValues.eavAttribute',
                'eavDoubleValues.eavAttribute',
            ])
            ->where(['id' => $productSkuId])
            ->one();
        $values = [];
        foreach ($productSku->eavVarcharValues as $value) {
            $values[ $value->attribute_id ][ $value->id ] = $value;
        }
        foreach ($productSku->eavTextValues as $value) {
            $values[ $value->attribute_id ][ $value->id ] = $value;
        }
        foreach ($productSku->eavDoubleValues as $value) {
            $values[ $value->attribute_id ][ $value->id ] = $value;
        }
        return $values;
    }

    /**
     * @param int $productTypeId
     * @return EavAttributeEntity[]
     */
    private function getProductTypeAttributes(int $productTypeId): array
    {
        /** @var EavAttributeEntity[] $productTypeAttributes */
        $attributes = EavAttributeEntity::find()
        ->innerJoin(
            ProductTypeAttributeEntity::tableName(),
            [
                ProductTypeAttributeEntity::tableName() . '.attribute_id' => new Expression(EavAttributeEntity::tableName() . '.id'),
            ]
        )->andWhere([
            ProductTypeAttributeEntity::tableName() . '.product_type_id' => $productTypeId,
        ])->indexBy('id')->all();
        return $attributes;
    }

    /**
     * @param string $valueStorageType
     * @param int $productSkuId
     * @param int $valueId
     * @throws Exception
     */
    public function createRelation(string $valueStorageType, int $productSkuId, int $valueId)
    {
        switch ($valueStorageType) {
            case EavAttributeEntity::STORAGE_TYPE_DOUBLE:
                $relation = new EavValueDoubleProductSkuEntity();
                break;
            case EavAttributeEntity::STORAGE_TYPE_VARCHAR:
                $relation = new EavValueVarcharProductSkuEntity();
                break;
            case EavAttributeEntity::STORAGE_TYPE_TEXT:
                $relation = new EavValueTextProductSkuEntity();
                break;
            default:
                throw new Exception('Non exist storage type.');
        }
        $relation->product_sku_id = $productSkuId;
        $relation->value_id = $valueId;
        $relation->save();
    }

    /**
     * @param EavAttributeEntity $attribute
     * @param int $valueId
     * @param int $productSkuId
     * @throws \Throwable
     */
    private function deleteRelation(
        EavAttributeEntity $attribute,
        int $valueId,
        int $productSkuId
    ) {
        switch ($attribute->storage_type) {
            case EavAttributeEntity::STORAGE_TYPE_DOUBLE:
                $query = EavValueDoubleProductSkuEntity::find();
                break;
            case EavAttributeEntity::STORAGE_TYPE_VARCHAR:
                $query = EavValueVarcharProductSkuEntity::find();
                break;
            case EavAttributeEntity::STORAGE_TYPE_TEXT:
                $query = EavValueTextProductSkuEntity::find();
                break;
            default:
                throw new Exception('Non exist storage type.');
        }
        $relation = $query->where([
            'value_id' => $valueId,
            'product_sku_id' => $productSkuId,
        ])->one();
        $relation->delete();
    }

    /**
     * @param int $attributeId
     * @param float $value
     * @param int|null $unitId
     * @return EavValueDoubleEntity
     * @throws Exception
     */
    public function createDoubleValue(int $attributeId, float $value, int $unitId = null): EavValueDoubleEntity
    {
        $newValue = new EavValueDoubleEntity();
        $newValue->attribute_id = $attributeId;
        $newValue->value = (string) $value;
        $newValue->value_type_unit_id = $unitId;
        $newValue->code = (string) $value;
        if (! $newValue->save()) {
            throw new Exception('Not save.');
        }
        return $newValue;
    }

    /**
     * @param int $attributeId
     * @param string $value
     * @return EavValueVarcharEntity
     * @throws Exception
     */
    public function createVarcharValue(int $attributeId, string $value): EavValueVarcharEntity
    {
        $newValue = new EavValueVarcharEntity();
        $newValue->attribute_id = $attributeId;
        $newValue->value = $value;
        $newValue->code = Inflector::slug($value);
        if (! $newValue->save()) {
            throw new Exception('Not save.');
        }
        return $newValue;
    }

    /**
     * @param int $attributeId
     * @param string $value
     * @return EavValueTextEntity
     * @throws Exception
     */
    public function createTextValue(int $attributeId, string $value): EavValueTextEntity
    {
        $newValue = new EavValueTextEntity();
        $newValue->attribute_id = $attributeId;
        $newValue->value = $value;
        if (! $newValue->save()) {
            throw new Exception('Not save.');
        }
        return $newValue;
    }

    /**
     * @param int $productSkuId
     * @param float $newValue
     * @param EavAttributeEntity $attributeEntity
     * @param EavValueDoubleEntity|null $oldValueEntity
     * @param int|null $unitId
     * @throws Exception
     * @throws \Throwable
     */
    private function updateDoubleValue(
        int $productSkuId,
        float $newValue,
        EavAttributeEntity $attributeEntity,
        EavValueDoubleEntity $oldValueEntity = null,
        int $unitId = null
    ) {
        if (! empty($oldValueEntity) && ($oldValueEntity->value != $newValue || $oldValueEntity->value_type_unit_id != $unitId)) {
            $this->deleteRelation($attributeEntity, $oldValueEntity->id, $productSkuId);
        }
        if (empty($oldValueEntity) || ($oldValueEntity->value != $newValue || $oldValueEntity->value_type_unit_id != $unitId)) {
            /** @var EavValueDoubleEntity $existValue */
            $existValue = EavValueDoubleEntity::find()
                ->where([
                    'attribute_id' => $attributeEntity->id,
                    'value' => $newValue,
                    'value_type_unit_id' => $unitId
                ])->one();
            if (empty($existValue)) {
                $createdValueEntity = $this->createDoubleValue($attributeEntity->id, $newValue, $unitId);
                $this->createRelation($attributeEntity->storage_type, $productSkuId, $createdValueEntity->id);
            } else {
                $this->createRelation($attributeEntity->storage_type, $productSkuId, $existValue->id);
            }
        }
    }

    /**
     * @param int $productSkuId
     * @param string $newValue
     * @param EavAttributeEntity $attributeEntity
     * @param EavValueVarcharEntity|null $oldValueEntity
     * @throws Exception
     * @throws \Throwable
     */
    private function updateVarcharValue(
        int $productSkuId,
        string $newValue,
        EavAttributeEntity $attributeEntity,
        EavValueVarcharEntity $oldValueEntity = null
    ) {
        if (! empty($oldValueEntity) && $oldValueEntity->value != $newValue) {
            $this->deleteRelation($attributeEntity, $oldValueEntity->id, $productSkuId);
        }
        if (empty($oldValueEntity) || $oldValueEntity->value != $newValue) {
            /** @var EavValueVarcharEntity $existValue */
            $existValue = EavValueVarcharEntity::find()
                ->where([
                    'attribute_id' => $attributeEntity->id,
                    'value' => $newValue
                ])->one();
            if (empty($existValue)) {
                $createdValueEntity = $this->createVarcharValue($attributeEntity->id, $newValue);
                $this->createRelation($attributeEntity->storage_type, $productSkuId, $createdValueEntity->id);
            } else {
                $this->createRelation($attributeEntity->storage_type, $productSkuId, $existValue->id);
            }
        }
    }

    /**
     * @param int $productSkuId
     * @param string $newValue
     * @param EavAttributeEntity $attributeEntity
     * @param EavValueTextEntity $oldValueEntity
     * @throws Exception
     */
    private function updateTextValue(
        int $productSkuId,
        string $newValue,
        EavAttributeEntity $attributeEntity,
        EavValueTextEntity $oldValueEntity = null
    ) {
        if (empty($oldValueEntity)) {
            $entity = $this->createTextValue($attributeEntity->id, $newValue);
            $this->createRelation($attributeEntity->storage_type, $productSkuId, $entity->id);
        } else {
            $oldValueEntity->value = $newValue;
            if (!$oldValueEntity->save()) {
                throw new Exception('Cant save double value.');
            }
        }
    }

    /**
     * @param EavValueDoubleEntity $entity
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function deleteValue(EavValueDoubleEntity $entity)
    {
        if(false === $entity->delete()) {
            throw new Exception('Cant delete double value.');
        }
    }
}
