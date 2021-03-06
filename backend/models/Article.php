<?php

namespace backend\models;

use Yii;


/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property integer $article_category_id
 * @property integer $sort
 * @property integer $status
 * @property integer $create_time
 */
class Article extends \yii\db\ActiveRecord
{
//    public $create_time;
    public static function statusOption($status){
        $options= [
            -1=>'删除',0=>'隐藏',1=>'正常'
        ];
//        if($hidden_del){
//            unset($options['-1']);
//        }
        return $options[$status];
    }
    public static function statusOptions($del=true){
        $options= [
            -1=>'删除',0=>'隐藏',1=>'正常'
        ];
        if($del){
            unset($options['-1']);
        }
        return $options;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','sort','intro'],'required'],
            [['intro'], 'string'],
            [[ 'sort', 'status', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'intro' => '简介',
            'article_category_id' => '文章分类id',
            'sort' => '排序',
            'status' => '状态',
            'create_time' => '创建时间',
//            'content'=>'内容'
        ];
    }
    public function getArticleCategory()
    {
        return $this->hasOne(ArticleCategory::className(),['id'=>'article_category_id']);
    }
}
