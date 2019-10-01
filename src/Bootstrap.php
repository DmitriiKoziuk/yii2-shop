<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use DmitriiKoziuk\yii2ConfigManager\ConfigManagerModule;
use DmitriiKoziuk\yii2ConfigManager\services\ConfigService;
use DmitriiKoziuk\yii2ModuleManager\services\ModuleRegistrationService;

final class Bootstrap implements BootstrapInterface
{
    /**
     * @param Application $app
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function bootstrap($app)
    {
        $app->setComponents([
            'dkShopQueue' => [
                'class' => \yii\queue\file\Queue::class,
                'path' => '@console/runtime/queue/dk-shop-queue',
                'as log' => \yii\queue\LogBehavior::class,
            ],
        ]);
        $app->bootstrap[] = 'dkShopQueue';
        ModuleRegistrationService::addModule(ShopModule::class, function () use ($app) {
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