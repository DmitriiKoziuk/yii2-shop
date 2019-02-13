Yii2 shop
========================
Yii2 shop.

## Info

The best practice is use this module/extension with [yii2 advanced application](https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/start-installation.md)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

1. Either run
    
    ```
    php composer.phar require dmitriikoziuk/yii2-shop
    ```
    
    or add
    
    ```
    "dmitriikoziuk/yii2-shop": "~0.3.0"
    ```
    
    to the require section of your `composer.json` file.
    
2. Run command 

    ```
    /path/to/php-bin/php /path/to/yii-application/yii migrate --migrationPath=@DmitriiKoziuk/yii2Shop/migrations
    ```
    
## Update from previous version

1. Change

    ```
    "dmitriikoziuk/yii2-shop": "~0.2.0"
    ```
    
    to
    
    ```
    "dmitriikoziuk/yii2-shop": "~0.3.0"
    ```

    in your `composer.json` file.
    
2. Run 

    ```
    /path/to/php-bin/php /path/to/composer-file/composer update
    ```

3. Run command 

    ```
    /path/to/php-bin/php /path/to/yii-application/yii migrate --migrationPath=@DmitriiKoziuk/yii2Shop/migrations
    ```