<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entities;

use Yii;
use yii\db\ActiveQuery;
use DmitriiKoziuk\yii2Shop\ShopModule;

/**
 * This is the model class for table "{{%dk_shop_eav_value_varchar_seo_values}}".
 *
 * @property int    $varchar_id
 * @property string $code
 * @property string $value
 *
 * @property EavValueVarcharEntity $varchar
 */
class EavValueVarcharSeoValueEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dk_shop_eav_value_varchar_seo_values}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['varchar_id', 'code', 'value'], 'required'],
            [['varchar_id'], 'integer'],
            [['code'], 'string', 'max' => 10],
            [['value'], 'string', 'max' => 255],
            [['varchar_id', 'code'], 'unique', 'targetAttribute' => ['varchar_id', 'code']],
            [
                ['varchar_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => EavValueVarcharEntity::class,
                'targetAttribute' => ['varchar_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'varchar_id' => Yii::t(ShopModule::TRANSLATION, 'Varchar ID'),
            'code'       => Yii::t(ShopModule::TRANSLATION, 'Code'),
            'value'      => Yii::t(ShopModule::TRANSLATION, 'Value'),
        ];
    }

    public function getVarchar(): ActiveQuery
    {
        return $this->hasOne(EavValueVarcharEntity::class, ['id' => 'varchar_id']);
    }
}
