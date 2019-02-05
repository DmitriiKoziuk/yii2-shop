<?php
namespace DmitriiKoziuk\yii2Shop\forms\product;

use yii\base\Model;

class ProductInputForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $name;
    public $slug;
    public $url;
    public $category_id;
    public $type_id;
    public $brand_id;

    public function rules()
    {
        return [
            [['name'], 'required',                  'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['name'], 'string', 'max' => 110,      'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['name'], 'trim',                      'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['slug'], 'required',                  'on' => [self::SCENARIO_UPDATE]],
            [['slug'], 'string', 'max' => 130,      'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['slug'], 'trim',                      'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['url'], 'required',                   'on' => [self::SCENARIO_UPDATE]],
            [['url'], 'string', 'max' => 255,       'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['url'], 'trim',                       'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['category_id', 'type_id'], 'integer', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['category_id', 'type_id', 'brand_id'], 'filter', 'filter' => function ($value) {
                    return empty($value) ? null : intval($value);
                },
                'on' => [self::SCENARIO_UPDATE]
            ],
        ];
    }
}