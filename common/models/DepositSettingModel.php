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
use yii\helpers\ArrayHelper;

/**
 * @Id DepositSettingModel.php 2018.4.2 $
 * @author mosir
 */


class DepositSettingModel extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%deposit_setting}}';
    }
	
	/* 取系统配置 */
	public static function getSystemSetting()
	{
		if(!($setting = parent::find()->where(['userid' => 0])->asArray()->one())) {
			$model = new DepositSettingModel();
			$model->userid = 0;
			$model->trade_rate = 0;
			$model->transfer_rate = 0;
			$model->regive_rate = 0;
			$model->save(false);
			
			$setting = ArrayHelper::toArray($model);
		}
		return $setting;
	}
	
	public static function getDepositSetting($userid = 0, $fields = null)
	{
		if(!$userid) {
			$setting = self::getSystemSetting();
		}
		elseif(!($setting = parent::find()->where(['userid' => $userid])->asArray()->one())) {
			$setting = self::getSystemSetting();
		}
		
		if(empty($fields) || !isset($setting[$fields])) {
			return $setting;
		} else {
			$result = $setting[$fields];
			return ($result < 0 || $result > 1) ? 0 : $result;
		}
	}
}
