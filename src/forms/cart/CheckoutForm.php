<?php
namespace DmitriiKoziuk\yii2Shop\forms\cart;

use Yii;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Base\forms\Form;

class CheckoutForm extends Form
{
    public $first_name;
    public $middle_name;
    public $last_name;
    public $phone_number;
    public $email;

    public function rules()
    {
        return [
            [['first_name', 'phone_number'], 'required'],
            [['first_name', 'middle_name', 'last_name', 'phone_number'], 'string', 'max' => 45],
            [['email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'first_name'   => Yii::t(ShopModule::TRANSLATION_CART, 'First name'),
            'middle_name'  => Yii::t(ShopModule::TRANSLATION_CART, 'Middle name'),
            'last_name'    => Yii::t(ShopModule::TRANSLATION_CART, 'Last name'),
            'phone_number' => Yii::t(ShopModule::TRANSLATION_CART, 'Phone number'),
        ];
    }
}