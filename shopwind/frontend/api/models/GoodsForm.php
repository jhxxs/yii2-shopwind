<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\api\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use common\models\GoodsModel;
use common\models\GoodsPropModel;
use common\models\GoodsPvsModel;
use common\models\GoodsPropValueModel;
use common\models\GcategoryModel;
use common\models\CategoryGoodsModel;
use common\models\GuideshopModel;
use common\models\IntegralSettingModel;
use common\models\DeliveryTemplateModel;
use common\models\RegionModel;
use common\models\CollectModel;
use common\models\UploadedFileModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Timezone;
use common\library\Def;

use frontend\api\library\Formatter;

/**
 * @Id GoodsForm.php 2018.10.23 $
 * @author yxyc
 */
class GoodsForm extends Model
{
	public $goods_id = 0;
	public $errors = null;

	public function formData($post)
	{
		$query = GoodsModel::find()->alias('g')->select('g.goods_id,g.type,g.store_id,g.goods_name,g.tags,g.if_show,g.isnew,g.cate_id,g.brand,g.spec_qty,g.spec_name_1,g.spec_name_2,g.add_time,g.default_spec,g.default_image,g.long_image,g.video, g.recommended,g.price,g.mkprice,g.dt_id,gs.stock,s.store_name,s.sgrade,gi.max_exchange,gst.sales,gst.comments,gst.views,gst.collects');
		if (isset($post->querydesc) && ($post->querydesc === true)) {
			$query->addSelect('g.description,g.content');
		}

		$query = $query->joinWith('store s', false)->joinWith('goodsDefaultSpec gs', false)->joinWith('goodsIntegral gi', false)->joinWith('goodsStatistics gst', false)
			->where(['g.goods_id' => $post->goods_id]);

		if (isset($post->if_show) && $post->if_show >= 0 && $post->if_show !== '') {
			if ($post->if_show) {
				$query->andWhere(['g.if_show' => 1, 'g.closed' => 0, 's.state' => Def::STORE_OPEN]);
			} else {
				$query->andWhere(['or', ['g.if_show' => 0], ['g.closed' => 1], ['!=', 's.state' => Def::STORE_OPEN]]);
			}
		}

		if (($record = $query->asArray()->one())) {
			$integral = ['enabled' => false];

			// 积分功能开启状态下
			if (IntegralSettingModel::getSysSetting('enabled')) {
				$integral['enabled'] = true;

				// 购买该商品可以使用的积分数
				$integral['exchange_rate'] = floatval(IntegralSettingModel::getSysSetting('rate'));
				$integral['exchange_money'] = floatval($integral['exchange_rate'] * $record['max_exchange']);
				$integral['exchange_integral'] = floatval($record['max_exchange']);

				// 计算送积分值
				$buygoods = IntegralSettingModel::getSysSetting('buygoods');
				if ($buygoods && ($giveRate = $buygoods[$record['sgrade']])) {
					$integral['give_integral'] = round($giveRate * $record['price']);
				}
			}
			unset($record['max_exchange'], $record['sgrade']);
			$record['integral'] = $integral;
			$record['default_image'] = Formatter::path($record['default_image'], 'goods');
			$record['long_image'] = Formatter::path($record['long_image']);
			$record['video'] = Formatter::path($record['video']);
			$record['add_time'] = Timezone::localDate('Y-m-d H:i:s', $record['add_time']);
			$record['category'] = GcategoryModel::getAncestor($record['cate_id'], 0, false);
			$record['scategory'] = CategoryGoodsModel::find()->select('cate_id')->where(['goods_id' => $post->goods_id])->column();

			// 商品是否被当前访客收藏
			if (!Yii::$app->user->isGuest) {
				$record['becollected'] = CollectModel::find()->where(['type' => 'goods', 'item_id' => $post->goods_id, 'userid' => Yii::$app->user->id])->exists();
			}
		}

		return $record;
	}

	/*
	 * 获取基础搜索条件
	 */
	public function getBasicConditions($post = null, $query = null)
	{
		// 指定店铺
		if (isset($post->store_id) && $post->store_id) {
			$query->andWhere(['s.store_id' => $post->store_id]);
		}

		// 指定关键词
		if (isset($post->keyword) && $post->keyword) {
			$query->andWhere(['or', ['like', 'g.goods_name', $post->keyword], ['like', 'g.brand', $post->keyword]]);
		}

		// 是否推荐
		if (isset($post->recommended) && $post->recommended !== '' && $post->recommended !== null) {
			$query->andWhere(['g.recommended' => intval($post->recommended)]);
		}

		// 是否上架
		if (isset($post->if_show) && $post->if_show >= 0 && $post->if_show !== '' && $post->if_show !== null) {
			$query->andWhere(['g.if_show' => intval($post->if_show)]);
			if ($post->if_show == 1) {
				$query->andWhere(['s.state' => 1, 'g.closed' => 0]);
			}
		}

		// 是否禁售
		if (isset($post->closed) && $post->closed !== '' && $post->closed !== null) {
			$query->andWhere(['g.closed' => intval($post->closed)]);
		}

		// 指定品牌
		if (isset($post->brand) && $post->brand) {
			$query->andWhere(['g.brand' => $post->brand]);
		}

		// 指定平台商品分类
		if (isset($post->cate_id) && $post->cate_id) {
			$allId = GcategoryModel::getDescendantIds($post->cate_id);
			$query->andWhere(['in', 'g.cate_id', $allId]);
		}

		// 指定店铺商品分类
		if (isset($post->scate_id) && $post->scate_id && $post->store_id) {
			$allId = GcategoryModel::getDescendantIds($post->scate_id, $post->store_id);
			$array = CategoryGoodsModel::find()->select('goods_id')->where(['in', 'cate_id', $allId])->column();
			$query->andWhere(['in', 'g.goods_id', $array]);
		}

		// 指定社区团购商品
		if ($post->channel == 'community') {
			if (($childs = GuideshopModel::getCategoryId(true)) !== false && !in_array($post->cate_id, $childs)) {
				$query->andWhere(['in', 'g.cate_id', $childs]);
			}
		}

		// 排序
		if (isset($post->orderby) && in_array($post->orderby, ['sales|desc', 'price|desc', 'price|asc', 'views|desc', 'add_time|desc', 'add_time|asc', 'comments|desc'])) {
			$orderBy = Basewind::trimAll(explode('|', $post->orderby));
			$query->orderBy([(in_array($orderBy[0], ['add_time', 'price']) ? 'g.' . $orderBy[0] : $orderBy[0]) => strtolower($orderBy[1]) == 'asc' ? SORT_ASC : SORT_DESC, 'g.goods_id' => SORT_DESC]);
		} else $query->orderBy(['gst.sales' => SORT_DESC, 'g.goods_id' => SORT_DESC]);

		return $query;
	}

	/**
	 * 获取搜索条件
	 */
	public function getConditions($post = null, $query = null)
	{
		if ($query === null) {
			$query = GoodsModel::find()->alias('g')->joinWith('goodsStatistics gst', false)->where(['s.state' => 1]);
		}
		$query->joinWith('store s', false)->joinWith('goodsDefaultSpec gs', false)->joinWith('goodsPvs gp', false);
		$query = $this->getBasicConditions($post, $query);

		// 指定价格区间
		if (isset($post->price) && $post->price && stripos($post->price, '-') > -1) {
			list($min, $max) = explode('-', $post->price);
			$min > 0 && $query->andWhere(['>=', 'g.price', floatval($min)]);
			$max > 0 && $query->andWhere(['<=', 'g.price', floatval($max)]);
		}

		// 指定地区
		if (isset($post->region_id) && $post->region_id) {
			$query->andWhere(['s.region_id' => $post->region_id]);
		}

		// 指定商品属性
		if (isset($post->props) && $post->props) {
			foreach (explode('|', $post->props) as $k => $pv) {
				// 监测是否全为数字
				if (is_numeric(str_replace(':', '', $pv))) {
					$query->andWhere("instr(gp.pvs,:pv$k) > 0", [":pv$k" => $pv]);
				}
			}
		}

		return $query;
	}

	/**
	 * 在一定的搜索条件下，获取还可用的商品检索字典
	 */
	public function getSelectors($post)
	{
		$result = [];

		// 按分类统计(把下级分类的商品数量也计算到父级)
		$by_category = [];
		$queryByCategory = $this->getConditions($post)->select('count(*) as count,g.cate_id')->groupBy('g.cate_id')->orderBy(['count' => SORT_DESC])->asArray()->all();
		foreach ($queryByCategory as $key => $val) {
			if (($group = GcategoryModel::getParnetEnd($val['cate_id'], $post->cate_id)) !== false) {
				if (isset($by_category[$group[0]])) {
					$by_category[$group[0]]['count'] += $val['count'];
				} else $by_category[$group[0]] = ['cate_id' => $group[0], 'cate_name' => $group[1], 'count' => $val['count']];
			}
		}
		$result['by_category'] = array_values($by_category);

		// 按品牌统计
		$by_brand = $this->getConditions($post)->select('count(*) as count,g.brand,b.logo')->joinWith('brand b', false)->andWhere(['b.if_show' => 1])->groupBy('g.brand,b.logo')->orderBy(['count' => SORT_DESC])->asArray()->all();
		foreach ($by_brand as $key => $value) {
			$by_brand[$key]['logo'] = Formatter::path($value['logo']);
		}
		$result['by_brand'] = $by_brand;

		// 按属性统计
		$by_props = [];
		$queryByProps = $this->getConditions($post)->select('gp.pvs')->andWhere(['<>', 'gp.pvs', ''])->column();
		$pvs = array_unique(explode(';', implode(';', array_values($queryByProps))));
		sort($pvs, SORT_DESC);

		// 检查属性名和属性值是否存在，有可能是之前有，但后面删除了
		foreach ($pvs as $key => $value) {
			list($pid, $vid) = explode(':', $value);
			if (!GoodsPropModel::find()->select('pid')->where(['pid' => $pid, 'status' => 1])->exists()) {
				unset($pvs[$key]);
			} elseif (!GoodsPropValueModel::find()->where(['pid' => $pid, 'vid' => $vid, 'status' => 1])->exists()) {
				unset($pvs[$key]);
			}
		}
		// 当前选中的属性数组
		$propChecked = array();
		if ($post->props) {
			$propChecked = array_unique(explode('|', $post->props));
			foreach ($propChecked as $key => $value) {
				list($p, $v) = explode(':', $value);
				$propChecked[] = $p;
				unset($propChecked[$key]);
			}
			$propChecked = array_unique($propChecked);
			sort($propChecked);
		}

		$propId = 0;
		foreach ($pvs as $key => $value) {
			list($pid, $vid) = explode(':', $value);

			// 将选中的排除
			if (!in_array($pid, $propChecked)) {
				$prop = GoodsPropModel::find()->select('pid,name,is_color')->where(['pid' => $pid, 'status' => 1])->one();
				$by_props[$prop->pid] = ArrayHelper::toArray($prop);

				// 不是同一个pid的属性值，不做累加
				if ($propId != $prop->pid) {
					$values = [];
					$propId = $prop->pid;
				}
				$values[] = GoodsPropValueModel::find()->select('vid,pid,pvalue as val,color')->where(['pid' => $pid, 'vid' => $vid, 'status' => 1])->asArray()->one();
				$by_props[$prop->pid] += array('values' => $values);
			}
		}
		// 统计每个属性有多少商品数
		if ($by_props) {
			foreach ($by_props as $key => $value) {
				if (!isset($value['values']) || empty($value['values'])) {
					unset($by_props[$key]);
					continue;
				}
				foreach ($value['values'] as $k => $v) {
					$by_props[$key]['values'][$k]['count'] = $this->getConditions($post)->select('g.goods_id')->andWhere('instr(gp.pvs,:pv) > 0', [':pv' => $v['pid'] . ':' . $v['vid']])->count();
				}
			}
		}
		$result['by_props'] = array_values($by_props);

		return ['selectors' => $result];
	}

	/** 
	 * 取得选中条件
	 */
	public function getFilters($post = null)
	{
		$filters = [];
		if (!empty($post->keyword)) {
			$filters['keyword'] = ['key' => 'keyword', 'category' => Language::get('keyword'), 'name' => $post->keyword, 'value' => $post->keyword];
		}
		if (!empty($post->brand)) {
			$filters['brand'] = ['key' => 'brand', 'category' => Language::get('brand'), 'name' => $post->brand, 'value' => $post->brand];
		}
		
		if ($post->props) {
			foreach (explode('|', $post->props) as $key => $value) {
				list($pid, $vid) = explode(':', $value);
				if (is_numeric($pid) && is_numeric($vid)) {
					$category = GoodsPropModel::find()->select('name')->where(['pid' => $pid, 'status' => 1])->scalar();
					$name = GoodsPropValueModel::find()->select('pvalue')->where(['pid' => $pid, 'vid' => $vid, 'status' => 1])->scalar();
					if ($category && $name) {
						$filters['props' . $key] = ['key' => 'props', 'category' => $category, 'name' => $name, 'value' => $value];
					}
				}
			}
		}
		if ($post->cate_id) {
			$array = GcategoryModel::find()->select('cate_name as name, cate_id as value')->where(['cate_id' => $post->cate_id])->asArray()->one();
			if ($array) {
				$filters['category'] = array_merge(['key' => 'cate_id', 'category' => Language::get('gcategory')], $array);
			}
		}

		return ['filters' => array_values($filters)];
	}

	/* 
	 * 获取商品属性信息
	 */
	public function getGoodProps($goods_id = 0)
	{
		$result = [];
		if (($pvs = GoodsPvsModel::find()->select('pvs')->where(['goods_id' => $goods_id])->scalar())) {
			foreach (explode(';', $pvs) as $pv) {
				if (!$pv) continue;
				list($pid, $vid) = explode(':', $pv);
				if (($prop = GoodsPropModel::find()->where(['pid' => $pid, 'status' => 1])->one())) {
					if (($value = GoodsPropValueModel::find()->where(['pid' => $pid, 'vid' => $vid, 'status' => 1])->one())) {
						if (isset($result[$pid]['value'])) $result[$pid]['value'] .= '，' . $value->pvalue;
						else $result[$pid] = array('name' => $prop->name, 'value' => $value->pvalue);
					}
				}
			}
		}

		return array_values($result);
	}

	/* 
	 * 获取指定城市的商品运费信息
	 * 返回多个运费方式(express/ems/post)的运费情况
	 */
	public function getLogistics($template_id, $city_id = 0, $store_id = 0)
	{
		if ($template_id) {
			$delivery = DeliveryTemplateModel::find()->where(['template_id' => $template_id])->asArray()->one();
		}

		// 如果商品没有设置运费模板，取店铺默认的运费模板
		if (empty($delivery)) {
			$delivery = DeliveryTemplateModel::find()->where(['store_id' => $store_id])->orderBy(['template_id' => SORT_ASC])->asArray()->one();

			// 如果还是没有，添加一条默认的
			if (empty($delivery)) {
				$delivery = DeliveryTemplateModel::addFirstTemplate($store_id);
			}
		}

		// 如果不传城市ID，则通过IP自动识别所在城市
		if (!$city_id) {
			$city_id = RegionModel::getCityIdByIp();
		}

		$record = [
			'region' => [
				'region_id' => $city_id,
				'region_name' => RegionModel::getRegionName($city_id),
			],
			'list' => DeliveryTemplateModel::getCityLogistic($delivery, $city_id),
		];

		return $record;
	}

	/**
	 * 获取商品详情页的图片(未使用)
	 */
	public function matchImages($content, $count = 100)
	{
		list($file_type) = UploadedFileModel::allowFile();

		$pattern = "/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/";
		preg_match_all($pattern, $content, $matches);

		$array = [];
		if (!empty($matches[1])) {

			// 循环匹配到的src
			foreach ($matches[1] as $src) {

				$src_real = strtok($src, '?'); //分割，去掉请求参数
				$ext = pathinfo($src_real, PATHINFO_EXTENSION); //获取拓展名

				if (in_array($ext, explode(',', $file_type))) {
					$array[]  = $src_real;

					if ($count > 0 && count($array) >= $count) {
						return $array;
					}
				}
			}
		}
		return $array;
	}
}
