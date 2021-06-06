<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use common\models\GuideshopModel;
use common\models\DepositSettingModel;
use common\models\GcategoryModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Message;
use common\library\Resource;
use common\library\Timezone;
use common\library\Page;
use common\library\Def;

/**
 * @Id GuideshopController.php 2020.2.4 $
 * @author mosir
 */

class GuideshopController extends \common\controllers\BaseAdminController
{
	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();
	}
	
	public function actionIndex()
	{
		$post = Basewind::trimAll(Yii::$app->request->get(), true, ['rp', 'page']);
	
		if(!Yii::$app->request->isAjax)
		{
			$this->params['filtered'] = $this->getConditions($post);
			
			$this->params['_foot_tags'] = Resource::import('jquery.plugins/flexigrid.js,inline_edit.js');
			
			$this->params['page'] = Page::seo(['title' => Language::get('list')]);
			return $this->render('../guideshop.index.html', $this->params);
		}
		else
		{
			$query = GuideshopModel::find()->select('id,userid,owner,phone_mob,name,region_name,address,created,status')->indexBy('id');
			$query = $this->getConditions($post, $query);
			
			$orderFields = ['name','owner', 'phone_mob', 'region_name','created','status'];
			if(in_array($post->sortname, $orderFields) && in_array(strtolower($post->sortorder), ['asc', 'desc'])) {
				$query->orderBy([$post->sortname => strtolower($post->sortorder) == 'asc' ? SORT_ASC : SORT_DESC]);
			} else $query->orderBy(['id' => SORT_DESC]);
			
			$page = Page::getPage($query->count(), $post->rp ? $post->rp : 10);
			
			$result = ['page' => $post->page, 'total' => $query->count()];
			foreach ($query->offset($page->offset)->limit($page->limit)->asArray()->each() as $key => $val)
			{
				$list = array();
				$operation = "<a class='btn red' onclick=\"fg_delete({$key},'guideshop')\"><i class='fa fa-trash-o'></i>删除</a>";
				if($post->status == 'applying') {
					$operation .= "<a class='btn orange' href='".Url::toRoute(['guideshop/verify', 'id' => $key])."'><i class='fa fa-check'></i>审核</a>";
				}
				$val['guider_rate'] = DepositSettingModel::getDepositSetting($val['userid'], 'guider_rate');

				$list['operation'] 	= $operation;
				$list['owner'] 		= $val['owner'];
				$list['phone_mob'] 	= $val['phone_mob'];
				$list['name'] 		= $val['name'];
				$list['address'] 	= $val['region_name'].$val['address'];
				$list['rate'] 		= '<span ectype="inline_edit" controller="guideshop" fieldname="guider_rate" fieldid="'.$key.'"  required="1" class="editable" title="'.Language::get('editable').'">'.$val['guider_rate'].'</span>';
				$list['status'] 	= $this->getStatus($val['status']);
				$list['created'] 	= Timezone::localDate('Y-m-d', $val['created']);
				$result['list'][$key] = $list;
			}
			return Page::flexigridXML($result);
		}
	}

	public function actionSetting()
	{
		if(!Yii::$app->request->isPost)
		{
			$setting = DepositSettingModel::find()->select('guider_rate')->where(['userid' => 0])->asArray()->one();
			if(($guideshop = Yii::$app->params['guideshop'])) {
				$setting = array_merge($setting, $guideshop);
			}
			$this->params['setting'] = $setting;
			$this->params['gcategories'] = GcategoryModel::getOptions(0, -1, null, 2);
			$this->params['page'] = Page::seo(['title' => Language::get('config')]);
			return $this->render('../guideshop.setting.html', $this->params);
		}
		else
		{
			$post = Basewind::trimAll(Yii::$app->request->post(), true);

			$model = new \backend\models\DepositSettingForm();
			if(!$model->save($post, true)) {
				return Message::warning($model->errors);
			}

			$post = ['guideshop' => ['cateId' => intval($post->cate_id)]];
			$model = new \backend\models\SettingForm();
			if(!$model->save($post, true)) {
				return Message::warning($model->errors);
			}

			return Message::display(Language::get('handle_ok'));
		}
	}

	public function actionVerify()
	{
		$get = Basewind::trimAll(Yii::$app->request->get(), true, ['id']);

		if(!Yii::$app->request->isPost)
		{
			$record = GuideshopModel::find()->select('id,owner,phone_mob,name,region_name,address,created,status,banner,remark')->where(['id' => $get->id])->asArray()->one();
			$record['status'] = $this->getStatus($record['status']);
			$this->params['guideshop'] = $record;
			
			$this->params['page'] = Page::seo(['title' => Language::get('verify')]);
			return $this->render('../guideshop.verify.html', $this->params);
		}
		else
		{
			$post = Basewind::trimAll(Yii::$app->request->post(), true);
			$model = GuideshopModel::findOne($get->id);

			// 待审核的店铺才允许提交，防止重复插入
			if($model && $model->status == Def::STORE_APPLYING)
			{
				// 批准
				if ($post->action == 'agree')
				{
					$model->status = Def::STORE_OPEN;
					$model->remark = '';
					if(!$model->save()) {
						return Message::warning(Language::get('handle_fail'));
					}

					return Message::display(Language::get('agree_ok'), ['guideshop/index']);
				}
				// 拒绝
				elseif($post->action == 'reject')
				{
					if (!$post->reason) {
						return Message::warning(Language::get('input_reason'));
					}
					
					$model->remark = $post->reason;
					$model->status = Def::STORE_NOPASS;
					if(!$model->save()) {
						return Message::warning(Language::get('handle_fail'));
					}

					return Message::display(Language::get('reject_ok'), ['guideshop/index', 'status' => 'applying']);
				}
			}
			return Message::warning(Language::get('handle_error'));
		}
	}
	
	/**
	 * 删除门店
	 */
	public function actionDelete()
	{
		$post = Basewind::trimAll(Yii::$app->request->get(), true);
		GuideshopModel::deleteAll(['in', 'id', explode(',', $post->id)]);
		return Message::display(Language::get('drop_ok'));
	}

	/* 异步修改数据 */
    public function actionEditcol()
    {
		$post = Basewind::trimAll(Yii::$app->request->get(), true);
		if(in_array($post->column, ['guider_rate'])) {
			if(!($userid = GuideshopModel::find()->select('userid')->where(['id' => $post->id])->scalar())) {
				return Message::warning(Language::get('edit_fail'));
			}
			$model = new \backend\models\DepositSettingForm(['userid' => $userid]);
			if(!$model->save((Object)[$post->column => $post->value], true)) {
				return Message::warning($model->errors);
			}
			return Message::display(Language::get('edit_ok'));
		}
    }

	/**
	 * 导出数据
	 */
	public function actionExport()
	{
		$post = Basewind::trimAll(Yii::$app->request->get(), true);
		if($post->id) $post->id = explode(',', $post->id);
		
		$query = GuideshopModel::find()->select('owner,phone_mob,name,region_name,address,created,status')->indexBy('id');
		if(!empty($post->id)) {
			$query->andWhere(['in', 'id', $post->id]);
		}
		else {
			$query = $this->getConditions($post, $query);
		}
		if($query->count() == 0) {
			return Message::warning(Language::get('no_data'));
		}
		return \backend\models\GuideshopExportForm::download($query->asArray()->all());		
	}
	
	private function getStatus($status = null)
	{
		$result = array(
            Def::STORE_APPLYING  => Language::get('applying'),
			Def::STORE_NOPASS	 => Language::get('nopass'),
            Def::STORE_OPEN      => Language::get('open'),
            Def::STORE_CLOSED    => Language::get('close'),
        );
		if($status !== null) {
			return isset($result[$status]) ? $result[$status] : '';
		}
		return $result;		
	}
	
	private function getConditions($post, $query = null)
	{
		if($query === null) {
			foreach(array_keys(ArrayHelper::toArray($post)) as $field) {
				if(in_array($field, ['name', 'status', 'owner', 'phone_mob'])) {
					return true;
				}
			}
			return false;
		}
		
		if($post->name) {
			$query->andWhere(['like', 'name', $post->name]);
		}
		if($post->owner) {
			$query->andWhere(['owner' => $post->owner]);
		}
		if($post->phone_mob) {
			$query->andWhere(['phone_mob' => $post->phone_mob]);
		}
		if($post->status == 'applying') {
			$query->andWhere(['in', 'status', [Def::STORE_APPLYING, Def::STORE_NOPASS]]);
		} else {
			$query->andWhere(['in', 'status', [Def::STORE_OPEN, Def::STORE_CLOSED]]);
		}

		return $query;
	}
}
