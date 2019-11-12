<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\helpers;

use Exception;
use yii\helpers\Url;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;

class CategoryProductFacetedNavigationHelper
{
    /**
     * @param string $indexPageUrl
     * @param EavAttributeEntity $attributeEntity
     * @param $valueEntity
     * @return string
     * @throws Exception
     */
    public static function createUrl(
        string $indexPageUrl,
        EavAttributeEntity $attributeEntity,
        $valueEntity
    ) {
        switch ($attributeEntity->storage_type) {
            case EavAttributeEntity::STORAGE_TYPE_VARCHAR:
                $value = $valueEntity->code;
                break;
            case EavAttributeEntity::STORAGE_TYPE_DOUBLE:
                $value = (string) $valueEntity->value;
                if (! empty($valueEntity->unit)) {
                    $value .= $valueEntity->unit->code;
                } elseif (! empty($attributeEntity->defaultValueTypeUnit)) {
                    $value .= $attributeEntity->defaultValueTypeUnit->code;
                }
                break;
            default:
                throw new Exception('Not supported attribute storage type.');
                break;
        }
        return Url::to([
            '/customUrl/create',
            'url' => $indexPageUrl,
            'filterParams' => [
                $attributeEntity->code => [
                    $value,
                ],
            ]
        ]);
    }
}