<?php
namespace DmitriiKoziuk\yii2Shop;

use Yii;
use yii\base\BootstrapInterface;
use DmitriiKoziuk\yii2ConfigManager\ConfigManagerModule;
use DmitriiKoziuk\yii2ConfigManager\services\ConfigService;
use DmitriiKoziuk\yii2ModuleManager\services\ModuleInitService;

final class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        ModuleInitService::registerModule(ShopModule::class, function () use ($app) {
            $app->setComponents([
                'dkShopQueue' => [
                    'class' => \yii\queue\file\Queue::class,
                    'path' => '@console/runtime/queue',
                    'as log' => \yii\queue\LogBehavior::class,
                ],
            ]);
            /** @var ConfigService $configService */
            $configService = Yii::$container->get(ConfigService::class);
            return [
                'class' => ShopModule::class,
                'diContainer' => Yii::$container,
                'queue' => $app->dkShopQueue,
                'dbConnection' => Yii::$app->db,
                'backendAppId' => $configService->getValue(
                    ConfigManagerModule::GENERAL_CONFIG_NAME,
                    'backendAppId'
                ),
                'frontendAppId' => $configService->getValue(
                    ConfigManagerModule::GENERAL_CONFIG_NAME,
                    'frontendAppId'
                ),
            ];
        });
    }
}