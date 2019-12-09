<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\services\eav;

use yii\helpers\Inflector;
use DmitriiKoziuk\yii2Base\traits\ModelValidatorTrait;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\forms\eav\EavAttributeCreateForm;
use DmitriiKoziuk\yii2Shop\repositories\EavAttributeRepository;

class EavAttributeService
{
    private $eavAttributeRepository;

    use ModelValidatorTrait;

    public function __construct(
        EavAttributeRepository $eavAttributeRepository
    ) {
        $this->eavAttributeRepository = $eavAttributeRepository;
    }

    public function createEavAttribute(EavAttributeCreateForm $createForm)
    {
        $this->validateModels([$createForm]);
        $newAttribute = new EavAttributeEntity();
        $newAttribute->setAttributes($createForm->getAttributes());
        $newAttribute->code = Inflector::slug($newAttribute->name);
        $this->eavAttributeRepository->save($newAttribute);
        return $newAttribute;
    }
}
