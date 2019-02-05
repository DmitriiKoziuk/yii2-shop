<?php
namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * This is the model class for table "{{%dk_shop_product_types}}".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $name_on_site
 * @property string  $code
 * @property string  $product_title
 * @property string  $product_description
 * @property string  $product_url_prefix
 * @property int     $created_at
 * @property int     $updated_at
 *
 * @property Product[] $products
 */
class ProductType extends ActiveRecord
{
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
            [['product_title'], 'string', 'max' => 255],
            [['product_description'], 'string', 'max' => 350],
            [['product_url_prefix'], 'string', 'max' => 100],
            [['name_on_site', 'product_title', 'product_description', 'product_url_prefix'], 'default', 'value' => NULL],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                  => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'ID'),
            'name'                => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Name'),
            'name_on_site'        => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Name on site'),
            'code'                => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Name'),
            'product_title'       => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Title'),
            'product_description' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Description'),
            'product_url_prefix'  => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Url prefix'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['type_id' => 'id']);
    }

    public function getProductNumber()
    {
        return $this->getProducts()->count();
    }

    public function getUrlPrefix()
    {
        return $this->product_url_prefix;
    }
}
