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

use common\models\UploadedFileModel;
use common\library\Setting;
use common\library\Def;
use common\library\Language;

/**
 * @Id SettingForm.php 2018.9.3 $
 * @author mosir
 */
class SettingForm extends Model
{
	public $errors = null;

	public function valid($post)
	{
		if (isset($post->upload) && $post->upload) {

			// 图片
			if ($post->upload->image_type) {
				$allows = explode(',', Def::IMAGE_FILE_TYPE);
				$types = explode(',', $post->upload->image_type);
				if ($types) {
					foreach ($types as $value) {
						if (!in_array($value, $allows)) {
							$this->errors = sprintf(Language::get('limit_img'), $value);
							return false;
						}
					}
				}
			}
			
			// 文件
			if ($post->upload->archive_type) {
				$allows = explode(',', Def::ARCHIVE_FILE_TYPE);
				$types = explode(',', $post->upload->archive_type);
				if ($types) {
					foreach ($types as $value) {
						if (!in_array($value, $allows)) {
							$this->errors = sprintf(Language::get('limit_file'), $value);
							return false;
						}
					}
				}
			}
		}

		return true;
	}

	public function save($post, $valid = true)
	{
		if ($valid === true && !$this->valid($post)) {
			return false;
		}

		$imageFileds = ['site_logo', 'default_store_logo', 'default_goods_image', 'default_user_portrait', 'iosqrcode', 'androidqrcode'];
		foreach ($imageFileds as $field) {
			if ($image = UploadedFileModel::getInstance()->upload($field, 'setting/', 0, 0, $field)) {
				$post->$field = $image;
			} else unset($post->$field);
		}

		return Setting::getInstance()->setAll($post);
	}
}
