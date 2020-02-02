<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\services\eav;

use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharSeoValueEntity;
use DmitriiKoziuk\yii2Shop\forms\eav\EavVarcharSeoValuesCompositeForm;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\repositories\EavValueVarcharSeoValueRepository;

class EavVarcharValueSeoService
{
    /**
     * @var EavValueVarcharSeoValueRepository
     */
    private $repository;

    public function __construct(
        EavValueVarcharSeoValueRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function updateSeoValues(
        EavValueVarcharEntity $varcharEntity,
        EavVarcharSeoValuesCompositeForm $form
    ): void {
        $existSeoValueEntities = $this->repository->getSeoValues($varcharEntity->id);
        foreach ($existSeoValueEntities as $seoValueEntity) {
            $code = $seoValueEntity->code;
            if (! array_key_exists($code, $form->seoValues)) {
                $this->repository->delete($seoValueEntity);
            } elseif ($seoValueEntity->value != $form->seoValues[ $code ]->value) {
                $seoValueEntity->value = $form->seoValues[ $code ]->value;
                $this->repository->save($seoValueEntity);
            }
            unset($form->seoValues[ $code ]);
        }
        foreach ($form->seoValues as $newSeoValueData) {
            if (! empty($newSeoValueData->value)) {
                $newSeoValueEntity = new EavValueVarcharSeoValueEntity([
                    'varchar_id' => $varcharEntity->id,
                    'code' => $newSeoValueData->code,
                    'value' => $newSeoValueData->value,
                ]);
                $this->repository->save($newSeoValueEntity);
            }
        }
    }
}