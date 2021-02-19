<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace backend\models;

use Yii;
use yii\base\Model;

use common\models\DepositSettingModel;

use common\library\Language;

/**
 * @Id DepositSettingForm.php 2018.8.6 $
 * @author mosir
 */
class DepositSettingForm extends Model
{
	public $userid = 0;
	public $errors = null;
	
    public function valid($post)
	{
		if(!$this->isNumeric($post, ['trade_rate', 'transfer_rate', 'regive_rate'])) {
			$this->errors = Language::get('number_error');
			return false;
		}

		if(($post->trade_rate < 0) || ($post->trade_rate >= 1) || ($post->transfer_rate < 0) || ($post->transfer_rate >= 1) || ($post->regive_rate < 0) || ($post->regive_rate >= 1) ) {
			$this->errors = Language::get('number_error');
			return false;
		}
		return true;
	}
	public function save($post, $valid = true)
	{
		if($valid === true && !$this->valid($post)) {
			return false;
		}
		
		if(!($model = DepositSettingModel::find()->where(['userid' => $this->userid])->one())) {
			$model = new DepositSettingModel();
		}
		$model->userid = $this->userid;
		$model->trade_rate = floatval($post->trade_rate);
		$model->transfer_rate = floatval($post->transfer_rate);
		$model->regive_rate = floatval($post->regive_rate);
		return $model->save() ? true : false;
	}
	private function isNumeric($post, $fields = array())
	{
		foreach($fields as $field) {
			if(!isset($post->$field) || !is_numeric($post->$field)) {
				return false;
			}
		}
		return true;
	}
}
