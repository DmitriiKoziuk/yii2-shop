<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharSeoValueEntity;

class EavValueVarcharSeoValueRepository extends AbstractActiveRecordRepository
{
    public function getCodeGroups(): array
    {
        return EavValueVarcharSeoValueEntity::find()
            ->select('code')
            ->groupBy('code')
            ->indexBy('code')
            ->asArray()
            ->all();
    }

    /**
     * @param int $valueId
     * @return EavValueVarcharSeoValueEntity[]
     */
    public function getSeoValues(int $valueId): array
    {
        return EavValueVarcharSeoValueEntity::find()
            ->where(['varchar_id' => $valueId])
            ->indexBy('code')
            ->all();
    }
}