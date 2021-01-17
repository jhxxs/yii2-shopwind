<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */

namespace frontend\models;

use Yii;
use yii\base\Model; 

use common\models\DistributeMerchantModel;
use common\library\Page;

/**
 * @Id DistributeTeamForm.php 2018.11.23 $
 * @author luckey
 */
class DistributeTeamForm extends Model
{
	public $errors = null;
	
	public function formData($post = null, $pageper = 4)
	{
		$post->id = $post->id ? $post->id : Yii::$app->user->id;
		$query = DistributeMerchantModel::find()->alias('dm')->select('dm.userid,dm.username,u.portrait')->joinWith('user u', false)->where(['parent_id' => $post->id])->orderBy(['add_time' => SORT_DESC]);
			
		$page = Page::getPage($query->count(), $pageper);
		$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
		foreach($list as $key => $val)
		{
			$list[$key]['portrait'] = empty($val['portrait']) ? Yii::$app->params['default_user_portrait'] : $val['portrait']; 
			$list[$key]['childcount'] = DistributeMerchantModel::find()->select('dmid')->where(['parent_id' => $val['userid']])->count();
		}
		return array($list, $page);
	}
}
