<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $create_at
 * @property integer $update_at
 * @property integer $last_login_time
 * @property string $last_login_ip
 */
class PasswordForm extends \yii\db\ActiveRecord
{
    public $oldPassword;
    public $newPassword;
    public $confirmPassword;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oldPassword','newPassword','confirmPassword'],'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'oldPassword'=>'旧密码',
            'newPassword'=>'新密码',
            'confirmPassword'=>'确认新密码'
        ];
    }
}
