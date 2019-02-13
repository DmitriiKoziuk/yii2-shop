<?php
namespace DmitriiKoziuk\yii2Shop\helpers;

use Yii;
use DmitriiKoziuk\yii2Shop\ShopModule;

class StringHelper
{
    public function getStringFromTemplate(string $templateString = null, array $params = []): ?string
    {
        if (empty($templateString)) {
            return null;
        } else {
            return Yii::t(ShopModule::TRANSLATION, $templateString, $params);
        }
    }
}