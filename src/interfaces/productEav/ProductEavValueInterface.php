<?php

namespace DmitriiKoziuk\yii2Shop\interfaces\productEav;

use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;

interface ProductEavValueInterface
{
    public function isUnitSet(): bool;

    public function getUnitAbbreviation(): string;

    public function getEavAttributeId(): int;

    public function getEavAttributeEntity(): ?EavAttributeEntity;
}
