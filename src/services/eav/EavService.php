<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\services\eav;

use Exception;
use InvalidArgumentException;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\helpers\EavAttributeHelper;
use DmitriiKoziuk\yii2Shop\repositories\EavRepository;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;

class EavService
{
    /**
     * @var EavRepository
     */
    private $eavRepository;

    public function __construct(EavRepository $eavRepository)
    {
        $this->eavRepository = $eavRepository;
    }

    /**
     * @param int $categoryId
     * @param EavAttributeEntity[] $filteredAttributes
     * @param array $filterParams
     * @return EavAttributeEntity[]
     * @throws Exception
     */
    public function getFacetedAttributesWithValues(
        int $categoryId,
        array $filteredAttributes = null,
        array $filterParams = []
    ): array {
        $varcharValues = $this->eavRepository->getFacetedVarcharValues($categoryId, $filteredAttributes, $filterParams);
        $doubleValues = $this->eavRepository->getFacetedDoubleValues($categoryId, $filteredAttributes, $filterParams);
        return EavAttributeHelper::buildFacetedAttributes($varcharValues, $doubleValues);
    }

    public function getFilteredAttributesWithValues(array $filterParams = []): array
    {
        $filteredAttributes = [];
        if (! empty($filterParams)) {
            $attributeCodes = $this->mapFilteredAttributeCodes($filterParams);
            $filteredAttributes = $this->eavRepository->getFilteredAttributes($attributeCodes);
            if (count($attributeCodes) != count($filteredAttributes)) {
                throw new InvalidArgumentException("One or more attribute not exist.");
            }
            $varcharValueCodes = $this->mapFilteredAttributesVarcharCodes($filteredAttributes, $filterParams);
            if (! empty($varcharValueCodes)) {
                $attributeIDs = ArrayHelper::map($this->filterVarcharAttributeIDs($filteredAttributes), 'id', 'id');
                $varcharValues = $this->eavRepository->getVarcharValuesByCodes($attributeIDs, $varcharValueCodes);
                if (count($varcharValueCodes) != count($varcharValues)) {
                    throw new InvalidArgumentException("One or more varchar values not exist.");
                }
                $this->mergeAttributesAndValues($filteredAttributes, $varcharValues);
            }
            $doubleValueCodes = $this->mapFilteredAttributesDoubleCodes($filteredAttributes, $filterParams);
            if (! empty($doubleValueCodes)) {
                $attributeIDs = ArrayHelper::map($this->filterDoubleAttributeIDs($filteredAttributes), 'id', 'id');
                $doubleValues = $this->eavRepository->getDoubleValuesByCodes($attributeIDs, $doubleValueCodes);
                if (count($doubleValueCodes) != count($doubleValues)) {
                    throw new InvalidArgumentException("One or more double values not exist.");
                }
                $this->mergeAttributesAndValues($filteredAttributes, $doubleValues);
            }
        }
        return $filteredAttributes;
    }

    public function removeAttributesFromProduct(Product $product)
    {
        $skus = $product->skus;
        foreach ($skus as $sku) {
            $this->eavRepository->removeProductSkuAndValuesRelations($sku->id);
        }
    }

    private function mapFilteredAttributeCodes(array $filterParams = null)
    {
        $codes = [];
        foreach ($filterParams as $key => $values) {
            if ('brand' != $key) {
                $codes[] = $key;
            }
        }
        return $codes;
    }

    /**
     * @param EavAttributeEntity[] $filteredAttributes
     * @param array $filterParams
     * @return array
     */
    private function mapFilteredAttributesVarcharCodes(array $filteredAttributes, array $filterParams): array
    {
        $codes = [];
        foreach ($filteredAttributes as $filteredAttribute) {
            if (
                array_key_exists($filteredAttribute->code, $filterParams) &&
                $filteredAttribute->storage_type === EavAttributeEntity::STORAGE_TYPE_VARCHAR
            ) {
                $codes = array_merge($codes, array_values($filterParams[ $filteredAttribute->code ]));
            }
        }
        return $codes;
    }

    /**
     * @param EavAttributeEntity[] $filteredAttributes
     * @param array $filterParams
     * @return array
     */
    private function mapFilteredAttributesDoubleCodes(array $filteredAttributes, array $filterParams): array
    {
        $codes = [];
        foreach ($filteredAttributes as $filteredAttribute) {
            if (
                array_key_exists($filteredAttribute->code, $filterParams) &&
                $filteredAttribute->storage_type === EavAttributeEntity::STORAGE_TYPE_DOUBLE
            ) {
                $codes = array_merge($codes, array_values($filterParams[ $filteredAttribute->code ]));
            }
        }
        return $codes;
    }

    /**
     * @param EavAttributeEntity[] $attributes
     * @param EavValueDoubleEntity[]|EavValueVarcharEntity[] $values
     */
    private function mergeAttributesAndValues(array $attributes, array $values)
    {
        foreach ($values as $value) {
            if (array_key_exists($value->attribute_id, $attributes)) {
                $attributes[ $value->attribute_id ]->values[] = $value;
            }
        }
    }

    /**
     * @param EavAttributeEntity[] $filteredAttributes
     * @return array
     */
    private function filterVarcharAttributeIDs(array $filteredAttributes): array
    {
        return array_filter($filteredAttributes, function ($attribute) {
            /** @var EavAttributeEntity $attribute */
            return EavAttributeEntity::STORAGE_TYPE_VARCHAR == $attribute->storage_type;
        });
    }

    /**
     * @param EavAttributeEntity[] $filteredAttributes
     * @return array
     */
    private function filterDoubleAttributeIDs(array $filteredAttributes): array
    {
        return array_filter($filteredAttributes, function ($attribute) {
            /** @var EavAttributeEntity $attribute */
            return EavAttributeEntity::STORAGE_TYPE_DOUBLE == $attribute->storage_type;
        });
    }
}
