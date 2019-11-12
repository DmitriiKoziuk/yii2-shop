<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\helpers;

use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;

class EavAttributeHelper
{
    /**
     * @param array $varcharValues
     * @param array $doubleValues
     * @return EavAttributeEntity[]
     */
    public static function buildFacetedAttributes(array $varcharValues, array $doubleValues): array
    {
        /** @var EavAttributeEntity[] $attributes */
        $attributes = [];
        foreach ($varcharValues as $varcharValue) {
            $attributeId = $varcharValue->eavAttribute->id;
            if (! array_key_exists($attributeId, $attributes)) {
                $attributes[ $attributeId ] = new EavAttributeEntity(
                    $varcharValue->eavAttribute->getAttributes()
                );
            }
            $attributes[ $attributeId ]->values[ $varcharValue->id ] = $varcharValue;
        }
        foreach ($doubleValues as $doubleValue) {
            $attributeId = $doubleValue->eavAttribute->id;
            if (! array_key_exists($attributeId, $attributes)) {
                $attributes[ $attributeId ] = new EavAttributeEntity(
                    $doubleValue->eavAttribute->getAttributes()
                );
            }
            $attributes[ $attributeId ]->values[ $doubleValue->id ] = $doubleValue;
        }
        ksort($attributes);
        return $attributes;
    }
}