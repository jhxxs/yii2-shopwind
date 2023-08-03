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

use common\models\BrandModel;
use common\models\UploadedFileModel;

use common\library\Language;

/**
 * @Id BrandForm.php 2018.8.13 $
 * @author mosir
 */
class BrandForm extends Model
{
	public $id = 0;
	public $errors = null;
	
	public function valid($post)
	{
		if(empty($post->name)) {
			$this->errors = Language::get('brand_empty');
			return false;
		}
		
		// edit
		if($this->id) {
			if(BrandModel::find()->where(['name' => $post->name])->andWhere(['!=', 'id', $this->id])->exists()) {
				$this->errors = Language::get('name_exist');
				return false;
			}
		}
		
		// add
		else {
			if(BrandModel::find()->where(['name' => $post->name])->exists()) {
				$this->errors = Language::get('name_exist');
				return false;
			}
		}

		return true;
	}
	
	public function save($post, $valid = true)
	{
		if($valid === true && !$this->valid($post)) {
			return false;
		}

		if(!$this->id || !($model = BrandModel::findOne($this->id))) {
			$model = new BrandModel();
		}
		
		$model->cate_id = $post->cate_id;
        $model->name = $post->name;
		$model->recommended = $post->recommended;
		$model->if_show = $post->if_show; 
		$model->sort_order = $post->sort_order ? $post->sort_order : 255;
		$model->tag = $post->tag;
		$model->letter = $post->letter;
		$model->description = $post->description;

		if(!$model->save()) {
			$this->errors = $model->errors;
			return false;
		}
			
		if(($image = UploadedFileModel::getInstance()->upload('logo', 'brand/', 0, $model->id, 'logo')) !== false) {
			$model->logo = $image;
			$model->save();
		}
		if(($image = UploadedFileModel::getInstance()->upload('image', 'brand/', 0, $model->id, 'image')) !== false) {
			$model->image = $image;
			$model->save();
		}
		return $model;
	}
}
