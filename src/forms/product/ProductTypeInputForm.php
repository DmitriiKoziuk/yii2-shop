<?php
namespace DmitriiKoziuk\yii2Shop\forms\product;

use yii\base\Model;

class ProductTypeInputForm extends Model
{
    public $name;
    public $name_on_site;
    public $product_title;
    public $product_description;
    public $product_url_prefix;
    public $margin_strategy;
    public $product_sku_title_template;
    public $product_sku_description_template;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'name_on_site'], 'string', 'max' => 45],
            [
                [
                    'product_title',
                    'product_sku_title_template',
                    'product_sku_description_template',
                ],
                'string',
                'max' => 255
            ],
            [['product_description'], 'string', 'max' => 350],
            [['product_url_prefix'], 'string', 'max' => 100],
            [['margin_strategy'], 'integer'],
            [
                [
                    'name',
                    'name_on_site',
                    'product_title',
                    'product_description',
                    'product_url_prefix'
                ],
                'trim'
            ],
        ];
    }
}