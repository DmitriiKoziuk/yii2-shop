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
        $app->setComponents([
            'dkShopQueue' => [
                'class' => \yii\queue\file\Queue::class,
                'path' => '@console/runtime/queue',
                'as log' => \yii\queue\LogBehavior::class,
            ],
        ]);
        $app->bootstrap[] = 'dkShopQueue';
        /** @var ConfigService $configService */
        $configService = Yii::$container->get(ConfigService::class);
        $app->setModule(ShopModule::ID, [
            'class' => ShopModule::class,
            'diContainer' => Yii::$container,
            'queue' => Yii::$app->dkShopQueue,
            'dbConnection' => Yii::$app->db,
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