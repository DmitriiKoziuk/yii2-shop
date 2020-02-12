<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\forms\order;

use yii\base\Model;

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
}