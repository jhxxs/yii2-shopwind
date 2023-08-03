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

use common\models\WeixinReplyModel;
use common\models\UploadedFileModel;

use common\library\Language;
use common\library\Timezone;

/**
 * @Id WeixinReplyForm.php 2018.8.28 $
 * @author mosir
 */
class WeixinReplyForm extends Model
{
	public $reply_id = 0;
	public $userid = 0;
	public $errors = null;
	
	public function valid($post)
	{
		if(!in_array($post->action, ['subscribe','text','keyword'])) {
			$this->errors = Language::get('action_invalid');
			return false;
		}

		if($post->action == 'keyword') {
			if(!isset($post->keywords) || empty($post->keywords)) {
				$this->errors = Language::get('keywords_empty');
				return false;
			}
		}
		
		if($post->type > 0) {
			if(!isset($post->title) || empty($post->title)) {
				$this->errors = Language::get('title_empty');
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

		if(!$this->reply_id || !($model = WeixinReplyModel::findOne($this->reply_id))) {
			if(in_array($post->action, ['subscribe','text']) && WeixinReplyModel::find()->where(['userid' => $this->userid, 'action' => $post->action])->exists()) {
				$this->errors = Language::get($post->action.'_add_already');
				return false;
			}
			$model = new WeixinReplyModel();
		}
		
		$fields = ['type', 'action', 'keywords', 'description'];
		foreach($post as $key => $val) {
			if(in_array($key, $fields)) $model->$key = $val;
		}
		$model->userid = $this->userid;
		$model->add_time = Timezone::gmtime();
		
		if($post->type > 0) 
		{
			$model->title = addslashes($post->title);
			$model->link = $post->link;
			if(($image = UploadedFileModel::getInstance()->upload('image', 'weixin/')) !== false) {
				$model->image = $image;
			}
		}
		
		if(!$model->save()) {
			$this->errors = $model->errors;
			return false;
		}
		return true;
	}
}
