<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\forms\order;

use Yii;
use yii\base\Model;
use DmitriiKoziuk\yii2Shop\ShopModule;

class OrderUpdateStatusForm extends Model
{
    public $stage_id;

    public $comment;

    public function rules()
    {
        return [
            [['stage_id'], 'required'],
            [['stage_id'], 'integer'],
            [['comment'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'stage_id' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Stage id'),
            'comment' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Stage comment'),
        ];
    }
}