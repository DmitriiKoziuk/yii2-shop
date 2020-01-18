<?php

namespace DmitriiKoziuk\yii2Shop\interfaces;

interface RepositorySearchMethodResponseInterface
{
    public function getTotalCount(): int;

    public function getItems(): array;
}