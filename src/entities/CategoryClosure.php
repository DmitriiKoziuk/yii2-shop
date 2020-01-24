<?php
namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveRecord;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * This is the model class for table "{{%dk_shop_category_closure}}".
 *
 * @property integer $id
 * @property integer $ancestor
 * @property integer $descendant
 * @property integer $depth
 */
class CategoryClosure extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dk_shop_category_closure}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ancestor', 'descendant', 'depth'], 'required'],
            [['ancestor', 'descendant', 'depth'], 'integer'],
            [
                ['descendant'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Category::class,
                'targetAttribute' => ['descendant' => 'id']
            ],
            [
                ['ancestor'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Category::class,
                'targetAttribute' => ['ancestor' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'ID'),
            'ancestor'   => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Ancestor'),
            'descendant' => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Descendant'),
            'depth'      => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Depth'),
        ];
    }

    public function init()
    {
    }

    public function afterFind()
    {
    }
}
