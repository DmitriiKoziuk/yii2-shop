<?php
namespace DmitriiKoziuk\yii2Shop\data\order;

use yii\base\Model;

class OrderSearchParams extends Model
{
    public $id;
    public $status;
    public $customerName;

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['customerName'], 'string']
        ];
    }
}