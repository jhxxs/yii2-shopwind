<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\home\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use common\models\GoodsModel;
use common\models\GoodsSpecModel;
use common\models\GoodsImageModel;
use common\models\GoodsPvsModel;
use common\models\GoodsIntegralModel;
use common\models\UploadedFileModel;
use common\models\GcategoryModel;
use common\models\StoreModel;
use common\models\DeliveryTemplateModel;
use common\models\CategoryGoodsModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Timezone;
use common\library\Promotool;
use common\library\Page;
use common\library\Plugin;

/**
 * @Id GoodsForm.php 2018.8.14 $
 * @author mosir
 */
class GoodsForm extends Model
{
	public $goods_id = 0;
	public $store_id = 0;
	public $gtype = 'material';
	public $errors = null;

	public function valid($post)
	{
		// 不是店家不允许发布商品
		if (!StoreModel::find()->where(['store_id' => $this->store_id, 'state' => 1])->exists()) {
			$this->errors = Language::get('not_seller');
			return false;
		}

		if ($this->goods_id) {
			$model = GoodsModel::find()->select('store_id,type')->where(['goods_id' => $this->goods_id])->one();
			if (!$model || $model->store_id != $this->store_id) {
				$this->errors = Language::get('no_such_goods');
				return false;
			}
			if (isset($post->type) && $model->type != $post->type) {
				$this->errors = Language::get('goods_type_editforbidden');
				return false;
			}
		} else {
			$settings = StoreModel::find()->alias('s')->select('sg.goods_limit')->joinWith('sgrade sg', false)->where(['store_id' => $this->store_id])->asArray()->one();
			$goodscounts = GoodsModel::find()->where(['store_id' => $this->store_id])->count();
			if ($goodscounts >= $settings['goods_limit']) {
				$this->errors = Language::get('goods_limit_arrived');
				return false;
			}
		}

		// 兼容新增和编辑，编辑模式下不传的字段不更新到数据库
		if ((isset($post->goods_name) && empty($post->goods_name)) || (!$this->goods_id && empty($post->goods_name))) {
			$this->errors = Language::get('goods_name_invalid');
			return false;
		}
		if ((isset($post->cate_id) && !$post->cate_id) || (!$this->goods_id && !$post->cate_id)) {
			$this->errors = Language::get('category_invalid');
			return false;
		}

		$images = ArrayHelper::toArray($post->goods_images);
		if ((isset($post->goods_images) && empty($images)) || (!$this->goods_id && empty($images))) {
			$this->errors = Language::get('goods_image_invalid');
			return false;
		}

		if (((isset($post->specs) || isset($post->price)) && !$this->validSpecs($post)) || (!$this->goods_id && !$this->validSpecs($post))) {
			return false;
		}

		if (isset($post->exclusive) && isset($post->exclusive->discount)) {
			if (!empty($post->exclusive->discount) && ($post->exclusive->discount > 9.99 || $post->exclusive->discount < 0.01)) {
				$this->errors = Language::get('discount_invalid');
				return false;
			}
		}

		return true;
	}

	public function save($post, $valid = true)
	{
		if ($valid === true && !$this->valid($post)) {
			return false;
		}

		if (!$this->goods_id || !($model = GoodsModel::find()->where(['goods_id' => $this->goods_id, 'store_id' => $this->store_id])->one())) {
			$model = new GoodsModel();
			$model->add_time = Timezone::gmtime();
			$model->store_id = $this->store_id;
			$model->type = isset($post->type) ? $post->type : $this->gtype;
		} else {
			$model->last_update = Timezone::gmtime();
		}

		// 编辑状态下，不传的字段不更新到数据库
		foreach (['goods_name', 'cate_id', 'tags', 'description', 'brand', 'long_image', 'video', 'recommended', 'if_show', 'isnew', 'dt_id'] as $field) {
			if (isset($post->$field)) {
				if (in_array($field, ['long_image', 'video'])) {
					$post->$field = str_replace(Basewind::baseUrl() . '/', '',  $post->$field);
				}
				$model->$field = $post->$field;
			}
		}

		if (($list = GcategoryModel::getAncestor($post->cate_id, 0, false))) {
			$ancestor = '';
			foreach ($list as $key => $value) {
				$ancestor .= $value['cate_name'] . ' ';
			}
			$model->cate_name = trim($ancestor);
		}

		// 运费模板
		if ($model->type != 'material') {
			$model->dt_id = 0;
		} else {
			if (!DeliveryTemplateModel::find()->where(['template_id' => $post->dt_id, 'store_id' => Yii::$app->user->id])->exists() && $post->delivery) {
				if ($array = DeliveryTemplateModel::addFirstTemplate(Yii::$app->user->id, $post->delivery)) {
					$model->dt_id = $array['template_id'];
				}
			}
		}

		if (!$model->save()) {
			$this->errors = $model->errors ? $model->errors : Language::get('save_fail');
			return false;
		}

		// 商品主图
		if (($images = $this->formatImages($post, 'goods_images'))) {

			// for update
			if ($list = GoodsImageModel::find()->select('image_id,thumbnail,image_url')->where(['goods_id' => $model->goods_id])->all()) {
				foreach ($list as $key => $value) {
					if (!in_array($value->thumbnail, $images) && !in_array($value->image_url, $images)) {
						$value->goods_id = 0;
						$value->save();
					}
				}
			}

			foreach ($images as $key => $value) {

				if ($key == 0) {
					$model->default_image = $value;
					$model->save();
				}

				$query = GoodsImageModel::find()->where(['and', ['in', 'goods_id', [0, $model->goods_id]], ['or', ['thumbnail' => $value], ['image_url' => $value]]])->one();
				if ($query) {
					$query->goods_id = $model->goods_id;
					$query->sort_order = $key + 1;
					if ($query->save()) {
						UploadedFileModel::updateAll(['item_id' => $model->goods_id], ['file_id' => $query->file_id]);
					}
				}
			}
		}

		// 描述图
		if (($images = $this->formatImages($post, 'desc_images'))) {
			foreach ($images as $key => $value) {
				UploadedFileModel::updateAll(['item_id' => $model->goods_id], ['file_path' => $value]);
			}
		}

		// 商品规格
		if ($specs = $this->formatSpecs($post)) {

			$allId = [];
			foreach ($specs as $key => $value) {
				if (!($query = GoodsSpecModel::find()->where(['goods_id' => $model->goods_id, 'spec_1' => $value['spec_1'], 'spec_2' => $value['spec_2']])->one())) {
					$query = new GoodsSpecModel();
					$query->goods_id = $model->goods_id;
				}

				foreach ($value as $k => $v) {
					if (in_array($k, ['spec_1', 'spec_2', 'price', 'mkprice', 'stock', 'image'])) {
						$query->$k = $v;
					}
				}
				if ($query->save()) {
					if ($key == 0) {
						$model->spec_qty = $post->spec_qty;
						$model->default_spec = $query->spec_id;
						$model->price = $value['price'];
						$model->mkprice = isset($value['mkprice']) ? $value['mkprice'] : 0;
						$model->save();
					}
				}
				$allId[] = $query->spec_id;
			}

			// 删除多余的规格
			GoodsSpecModel::deleteAll(['and', ['goods_id' => $model->goods_id], ['not in', 'spec_id', $allId]]);

			if ($post->spec_qty < 1) {
				$model->spec_name_1 = '';
				$model->spec_name_2 = '';
			} else {
				$model->spec_name_1 = $post->spec_name_1;
				$model->spec_name_2 = $post->spec_qty > 1 ? $post->spec_name_2 : '';
			}
			$model->save();
		}

		// 保存商品抵扣积分
		if (isset($post->integral_exchange)) {
			$this->saveIntegral($model->goods_id, $post);
		}

		// 保存商品属性
		if (isset($post->attributes)) {
			$this->saveAttributes($model->goods_id, $post);
		}

		// 保存手机专享设置
		if (isset($post->exclusive)) {
			$this->saveExclusive($model->goods_id, $post);
		}

		// 保存本店商品分类
		if (isset($post->scate_id)) {
			$this->saveSCategory($model->goods_id, $post);
		}

		if (!$this->goods_id) {
			$this->goods_id = $model->goods_id;
		}

		return true;
	}

	/**
	 * 保存商品积分设置
	 */
	private function saveIntegral($goods_id, $post)
	{
		if (!($model = GoodsIntegralModel::find()->where(['goods_id' => $goods_id])->one())) {
			$model = new GoodsIntegralModel();
			$model->goods_id = $goods_id;
		}
		$model->max_exchange = $post->integral_exchange;
		return $model->save();
	}

	/**
	 * 保存商品属性
	 */
	private function saveAttributes($goods_id, $post)
	{
		$attributes = [];
		foreach ($post->attributes as $key => $value) {
			if (is_object($value)) {
				foreach ($value as $k => $v) {
					if ($v) $attributes[] = $key . ':' . $v;
				}
			} elseif ($value) $attributes[] = $key . ':' . $value;
		}

		if (empty($attributes)) {
			GoodsPvsModel::deleteAll(['goods_id' => $goods_id]);
			return true;
		} else {
			if (!($model = GoodsPvsModel::find()->where(['goods_id' => $goods_id])->one())) {
				$model = new GoodsPvsModel();
				$model->goods_id = $goods_id;
			}
			$model->pvs = implode(';', $attributes);
			return $model->save();
		}
	}

	/**
	 * 保存手机专享设置
	 */
	private function saveExclusive($goods_id, $post)
	{
		$exclusiveTool = Promotool::getInstance('exclusive')->build(['store_id' => Yii::$app->user->id]);
		if ($exclusiveTool->checkAvailable(false)) {

			$config = [];
			if ($status = intval($post->exclusive->status)) {
				unset($post->exclusive->status);
				$config = ArrayHelper::toArray($post->exclusive);
			}

			$exclusiveTool->savePromotoolItem(['goods_id' => $goods_id, 'config' => $config, 'status' => $status]);
		}
	}

	/**
	 * 保存本店商品分类
	 */
	private function saveSCategory($goods_id, $post)
	{
		if (is_array($post->scate_id) || is_object($post->scate_id)) {
			CategoryGoodsModel::deleteAll(['goods_id' => $goods_id]);
			foreach ($post->scate_id as $value) {
				$model = new CategoryGoodsModel();
				$model->goods_id = $goods_id;
				$model->cate_id = $value;
				$model->save();
			}
		}

		return true;
	}

	/**
	 * 格式化上传图片，如果是本地上传（相对于OSS存储），则保存相对路径，不要存绝对路径
	 */
	private function formatImages($post, $field = 'goods_images')
	{
		if (!isset($post->$field)) {
			return [];
		}

		$images = [];
		foreach ($post->$field as $key => $value) {
			if ($value) {
				$images[] = str_replace(Basewind::baseUrl() . '/', '',  $value);
			}
		}

		return $images;
	}

	private function validSpecs($post = null)
	{
		if (!isset($post->specs)) {
			if ((!isset($post->price) || $post->price === '' || !isset($post->stock) || $post->stock === '')) {
				$this->errors = Language::get('price_invalid');
				return false;
			}
		} else {
			//$specs = ArrayHelper::toArray($post->specs);
			foreach ($post->specs as $value) {
				if ($value->price === '' || $value->stock === '') {
					$this->errors = Language::get('price_invalid');
					return false;
				}
				if (!$value->spec_1 && !$value->spec_2) {
					$this->errors = Language::get('goods_spec_invalid');
					return false;
				}
				if ($post->spec_qty == 2 && (!isset($post->spec_name_2) || !$post->spec_name_2)) {
					$this->errors = Language::get('goods_spec_invalid');
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * 处理商品规格数据
	 */
	private function formatSpecs($post = null)
	{
		// 不传的参数不更新到数据库（针对编辑模式）
		if (!isset($post->specs) && !isset($post->price)) {
			return [];
		}

		// 没有规格
		if (!isset($post->specs) || empty($specs = ArrayHelper::toArray($post->specs)) || $post->spec_qty <= 0) {
			return array([
				'price' => floatval($post->price),
				'mkprice' => isset($post->mkprice) ? floatval($post->mkprice) : 0,
				'stock' => intval($post->stock),
			]);
		}

		// 检查重复
		$values = [];
		foreach ($specs as $key => $value) {
			$item = $value['spec_1'] . $value['spec_2'];
			if (in_array($item, $values)) {
				$this->errors = Language::get('spec_repeat');
				return false;
			}
			$values[] = $item;

			foreach ($value as $k => $v) {
				if (in_array($k, ['price', 'mkprice', 'stock'])) {
					$specs[$key][$k] = floatval($v);
				}
				if ($k == 'image') {
					$specs[$key][$k] = str_replace(Basewind::baseUrl() . '/', '',  $v);
				}
			}
		}

		return $specs;
	}

	/**
	 * 从第三方平台采集数据导入到本地系统
	 */
	public function import($post, $itemid)
	{
		$cache = Yii::$app->cache;
		$cachekey = md5((__METHOD__) . var_export(func_get_args(), true));
		$result = $cache->get($cachekey);

		if ($result === false) {
			$client = Plugin::getInstance('datapicker')->build($post->code, $post);
			if (!$client || !($result = $client->detail($itemid))) {
				$this->errors = $client->errors;
				return false;
			}
			$cache->set($cachekey, $result, 3600 * 24 * 2);
		}

		$model = new GoodsModel();
		$model->store_id = $this->store_id;
		$model->add_time = Timezone::gmtime();

		$model->goods_name = $result['goods_name'];
		$model->spec_name_1 = $result['spec_name_1'];
		$model->spec_name_2 = $result['spec_name_2'];
		$model->spec_qty = $result['spec_qty'];
		$model->tags = $result['tags'];
		$model->description = $result['description'];
		$model->video = $result['video'] ? $result['video'] : '';

		$model->cate_id = $post->cate_id;
		$model->cate_name = '';
		$gcategories = GcategoryModel::getAncestor($post->cate_id);
		foreach ($gcategories as $key => $value) {
			$model->cate_name .= (($key == 0) ? "" : "\t") . $value['cate_name'];
		}

		if (!$model->save()) {
			$this->errors = $model->errors;
			return false;
		}

		// 保存商品主图
		foreach ($result['goods_images'] as $key => $value) {
			$imageModel = new GoodsImageModel();
			$imageModel->goods_id = $model->goods_id;
			foreach ($value as $k => $v) {
				$imageModel->$k = $v;
			}
			if ($imageModel->save()) {
				if ($key == 0) {
					$model->default_image = $imageModel->thumbnail;
					$model->save();
				}
			}
		}

		// 保存商品规格
		if ($result['specs']) {
			foreach ($result['specs'] as $key => $value) {
				$specModel = new GoodsSpecModel();
				$specModel->goods_id = $model->goods_id;
				foreach ($value as $k => $v) {
					$specModel->$k = $v;
				}
				if ($specModel->save()) {
					if ($key == 0) {
						$model->default_spec = $specModel->spec_id;
						$model->price = $specModel->price;
						$model->save();
					}
				}
			}
		}

		if (!$this->goods_id) {
			$this->goods_id = $model->goods_id;
		}

		return true;
	}
}
