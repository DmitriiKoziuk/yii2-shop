<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * This is the model class for table "{{%dk_shop_product_types}}".
 *
 * @property int     $id
 * @property string  $name
 * @property string  $name_on_site
 * @property string  $code
 * @property string  $product_title
 * @property string  $product_description
 * @property string  $product_url_prefix
 * @property int     $created_at
 * @property int     $updated_at
 * @property int     $margin_strategy
 * @property string  $product_sku_title_template
 * @property string  $product_sku_description_template
 *
 * @property Product[] $products
 * @property EavAttributeEntity[] $eavAttributeEntities
 */
class ProductType extends ActiveRecord
{
    const MARGIN_STRATEGY_NOT_SET = '';
    const MARGIN_STRATEGY_USE_AVERAGE_SUPPLIER_PURCHASE_PRICE = 1;
    const MARGIN_STRATEGY_USE_LOWER_SUPPLIER_PURCHASE_PRICE = 2;
    const MARGIN_STRATEGY_USE_HIGHEST_SUPPLIER_PURCHASE_PRICE = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dk_shop_product_types}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name'], 'unique'],
            [['name', 'name_on_site'], 'string', 'max' => 45],
            [['code'], 'unique'],
            [['code'], 'string', 'max' => 55],
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
            [
                [
                    'name_on_site',
                    'product_title',
                    'product_description',
                    'product_url_prefix',
                    'margin_strategy',
                    'product_sku_title_template',
                    'product_sku_description_template',
                ],
                'default',
                'value' => NULL
            ],
            [['created_at', 'updated_at', 'margin_strategy'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'ID'),
            'name' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Name'),
            'name_on_site' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Name on site'),
            'code' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Name'),
            'product_title' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Title'),
            'product_description' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Description'),
            'product_url_prefix' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Url prefix'),
            'margin_strategy' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Margin strategy'),
            'product_sku_title_template' => Yii::t(
                ShopModule::TRANSLATION_PRODUCT_TYPE, 'Product sku title template'
            ),
            'product_sku_description_template' => Yii::t(
                ShopModule::TRANSLATION_PRODUCT_TYPE, 'Product sku description template'
            ),
        ];
    }

    public function init()
    {
    }

    public function afterFind()
    {
    }

    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(Product::class, ['type_id' => 'id']);
    }

    /**
     * @throws InvalidConfigException
     */
    public function getEavAttributeEntities(): ActiveQuery
    {
        return $this->hasMany(EavAttributeEntity::class, ['id' => 'attribute_id'])
            ->viaTable(ProductTypeAttributeEntity::tableName(), ['product_type_id' => 'id']);
    }

    public function getProductNumber()
    {
        return $this->getProducts()->count();
    }

    public function getUrlPrefix()
    {
        return $this->product_url_prefix;
    }

    public function getProductSkuTitleTemplate()
    {
        return $this->product_sku_title_template;
    }

    public function getProductSkuDescriptionTemplate()
    {
        return $this->product_sku_description_template;
    }
}
