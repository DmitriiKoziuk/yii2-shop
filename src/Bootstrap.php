<?php
namespace DmitriiKoziuk\yii2Shop;

use Yii;
use yii\base\BootstrapInterface;
use DmitriiKoziuk\yii2ConfigManager\ConfigManager;
use DmitriiKoziuk\yii2ConfigManager\services\ConfigService;
use DmitriiKoziuk\yii2ModuleManager\services\ModuleService;

final class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function bootstrap($app)
    {
        /** @var ConfigService $configService */
        $configService = Yii::$container->get(ConfigService::class);
        $app->setModule(ShopModule::ID, [
            'class' => ShopModule::class,
            'dbConnection' => Yii::$app->db,
            'diContainer' => Yii::$container,
            'backendAppId' => $configService->getValue(
                ConfigManager::GENERAL_CONFIG_NAME,
                'backendAppId'
            ),
            'frontendAppId' => $configService->getValue(
                ConfigManager::GENERAL_CONFIG_NAME,
                'frontendAppId'
            ),
        ]);
        /** @var ShopModule $module */
        $module = $app->getModule(ShopModule::ID);
        /** @var ModuleService $moduleService */
        $moduleService = Yii::$container->get(ModuleService::class);
        $moduleService->registerModule($module);
    }
}