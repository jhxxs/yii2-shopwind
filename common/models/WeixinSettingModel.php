<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @Id WeixinSettingModel.php 2018.8.27 $
 * @author mosir
 */

class WeixinSettingModel extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%weixin_setting}}';
    }
	
	public static function genToken($length = 8) 
	{  
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$token = '';  
		for ( $i = 0; $i < $length; $i++ ) {   
			$token .= $chars[mt_rand(0, strlen($chars) - 1)];  
		}  
		return $token;  
	}
	
	public static function getConfig($userid = 0)
	{
		return parent::find()->where(['userid' => $userid])->orderBy(['id' => SORT_DESC])->asArray()->one();
	} 
}
