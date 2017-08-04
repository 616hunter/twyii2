<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shop_address".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $address
 * @property integer $status
 */
class ShopAddress extends \yii\db\ActiveRecord
{
    public $province_id;
    public $city_id;
    public $area_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tel','address','province_id','city_id','area_id','name'], 'required'],
            [['address'], 'string', 'max' => 255],
            ['status','safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'address' => 'Address',
            'status' => '设置为默认收货地址',
        ];
    }
}
