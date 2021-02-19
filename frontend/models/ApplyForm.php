<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\models;

use Yii;
use yii\base\Model; 

use common\models\StoreModel;
use common\models\SgradeModel;
use common\models\UploadedFileModel;
use common\models\CategoryStoreModel;
use common\models\IntegralModel;
use common\models\IntegralSettingModel;
use common\models\DeliveryTemplateModel;

use common\library\Language;
use common\library\Timezone;
use common\library\Def;

/**
 * @Id ApplyForm.php 2018.10.4 $
 * @author mosir
 */
class ApplyForm extends Model
{
	public $store_id = 0;
	public $errors = null;
	
	public function valid($post = null)
	{
		if(empty($post->store_name)) {
			$this->errors = Language::get('input_store_name');
			return false;
		}
		if(($store = StoreModel::find()->select('store_id')->where(['store_name' => $post->store_name])->one())) {
			if(!$this->store_id || ($this->store_id != $store->store_id)) {
				$this->errors = Language::get('name_exist');
				return false;	
			}
		}
		if(strlen($post->store_name) < 6 || strlen($post->store_name) > 60) {
			$this->errors = Language::get('note_for_store_name');
			return false;
		}
		
		if(empty($post->owner_name)) {
			$this->errors = Language::get('note_for_owner_name');
			return false;
		}
		return true;
	}
	
	public function save($post, $valid = true)
	{
		if($valid === true && !$this->valid($post)) {
			return false;
		}
		
		if(!$this->store_id || !($model = StoreModel::findOne($this->store_id))) {
			$model = new StoreModel();
			$model->store_id = Yii::$app->user->id;
			$model->sort_order = 255;
			$model->add_time = Timezone::gmtime();
		}
		$fields = ['store_name', 'owner_name', 'identity_card', 'region_id', 'region_name', 'address', 'zipcode', 'tel', 'sgrade'];
		foreach($fields as $key => $val) {
			$model->$val = $post->$val;
		}
		$model->state = SgradeModel::find()->select('need_confirm')->where(['grade_id' => $post->sgrade])->scalar() ? 0 : 1;
		$model->apply_remark = '';// 以便再次审核
		
		$fields = ['identity_front', 'identity_back', 'business_license'];
		foreach($fields as $key => $val) {
			if(($image = UploadedFileModel::getInstance()->upload($val, $model->store_id, Def::BELONG_IDENTITY, 0, $val))) {
				$model->$val = $image;
			}
		}
		
		if(!$model->save()) {
			$this->errors = $model->errors ? $model->errors : Language::get('apply_fail');
			return false;
		}
			
       	if($post->cate_id > 0)
  		{
			CategoryStoreModel::deleteAll(['store_id' => $model->store_id]);
				
			$query = new CategoryStoreModel();
			$query->store_id = $model->store_id;
			$query->cate_id = $post->cate_id;
			$query->save();          
        }
		
		// 添加一条默认的运费模板（不用等开通后才添加，因为提交后，没有审核通过，也是可以编辑编辑信息的）
		DeliveryTemplateModel::addFirstTemplate($model->store_id);
		
		// 不需要审核，店铺直接开通
		if($model->state)
		{
			// 给商家赠送开店积分
			IntegralModel::updateIntegral([
				'userid'  => Yii::$app->user->id,
				'type'    => 'openshop',
				'amount'  => IntegralSettingModel::getSysSetting('openshop')
			]);
		}
		
		return $model;
	}
}
