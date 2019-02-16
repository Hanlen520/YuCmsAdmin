<?php
/**
 * 设备模型
 * @author yuzhijie
 * @date 2013-10-13
 */
class DevicesImage extends ActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{devices_image}}';
    }

    public function rules()
    {
        return array(
        );
    }

    public function relations()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array(
        );
    }
}