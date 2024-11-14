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

use common\models\GoodsModel;
use common\models\ArticleModel;
use common\models\AcategoryModel;
use common\models\GcategoryModel;
use common\models\CouponModel;
use common\models\LimitbuyModel;
use common\models\TeambuyModel;

use common\library\Basewind;
use common\library\Message;
use common\library\Language;
use common\library\Timezone;
use common\library\Page;
use common\library\Promotool;

/**
 * @Id GselectorController.php 2018.3.29 $
 * @author mosir
 */

class GselectorController extends \common\base\BaseAdminController
{
	public function actionGoods()
	{
		if (!Yii::$app->request->isPost) {
			$this->params['gcategories'] = GcategoryModel::getOptions(0, -1, null, 2);
			$this->params['page'] = Page::seo(['title' => Language::get('gselector')]);
			return $this->render('../gselector.goods.html', $this->params);
		} else {

			$post = Basewind::trimAll(Yii::$app->request->post(), true, ['cate_id']);

			$query = GoodsModel::find()->alias('g')->select('goods_id,goods_name,brand, default_image,price')
				->joinWith('store s', false)
				->where(['s.state' => 1, 'g.if_show' => 1, 'g.closed' => 0]);

			if ($post->cate_id) {
				$childIds = GcategoryModel::getDescendantIds($post->cate_id, 0);
				$query->andWhere(['in', 'cate_id', $childIds]);
			}
			if ($post->keyword) {
				$query->andWhere(['or', ['like', 'goods_name', $post->keyword], ['like', 'brand', $post->keyword]]);
			}

			$page = Page::getPage($query->count(), 5, true, $post->page);
			$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();

			return Message::result(['list' => $list, 'pagination' => Page::formatPage($page, true, 'basic')]);
		}
	}

	/**
	 * 商品分类
	 */
	public function actionCategory()
	{
		$post = Basewind::trimAll(Yii::$app->request->get(), true, ['id']);
		if (!isset($post->id)) $post->id = 0;

		$list = GcategoryModel::getList($post->id, 0, true, 0, 'cate_id,cate_name,parent_id');
		foreach ($list as $key => $value) {
			if (GcategoryModel::find()->select('cate_id')->where(['parent_id' => $value['cate_id'], 'if_show' => 1])->exists()) {
				$list[$key]['switchs'] = 1;
			}
		}
		if (!Yii::$app->request->isPost) {
			$this->params['list'] = $list;

			$this->params['page'] = Page::seo(['title' => Language::get('gselector')]);
			return $this->render('../gselector.category.html', $this->params);
		}

		return Message::result(array_values($list));
	}

	public function actionAcategory()
	{
		$post = Basewind::trimAll(Yii::$app->request->get(), true, ['id']);
		if (!isset($post->id)) $post->id = 0;

		$list = AcategoryModel::getList($post->id, 0, true, 0, 'cate_id,cate_name,parent_id');
		foreach ($list as $key => $value) {
			if (AcategoryModel::find()->select('cate_id')->where(['parent_id' => $value['cate_id'], 'if_show' => 1])->exists()) {
				$list[$key]['switchs'] = 1;
			}
		}
		if (!Yii::$app->request->isPost) {
			$this->params['list'] = $list;

			$this->params['page'] = Page::seo(['title' => Language::get('aselector')]);
			return $this->render('../gselector.acategory.html', $this->params);
		}

		return Message::result(array_values($list));
	}

	public function actionArticle()
	{
		if (!Yii::$app->request->isPost) {
			$this->params['page'] = Page::seo(['title' => Language::get('aselector')]);
			return $this->render('../gselector.article.html', $this->params);
		} else {

			$post = Basewind::trimAll(Yii::$app->request->post(), true);

			$query = ArticleModel::find()->select('article_id,title,add_time')
				->where(['if_show' => 1, 'store_id' => 0]);

			$page = Page::getPage($query->count(), 6, true, $post->page);
			$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
			foreach ($list as $key => $value) {
				$list[$key]['add_time'] = Timezone::localDate('Y-m-d H:i:s', $value['add_time']);
			}

			return Message::result(['list' => $list, 'pagination' => Page::formatPage($page, true, 'basic')]);
		}
	}

	public function actionCoupon()
	{
		if (!Yii::$app->request->isPost) {
			$this->params['page'] = Page::seo(['title' => Language::get('gselector')]);
			return $this->render('../gselector.coupon.html', $this->params);
		} else {

			$post = Basewind::trimAll(Yii::$app->request->post(), true);

			$query = CouponModel::find()->alias('c')->select('id,name, money, amount, total, surplus, c.end_time, s.store_name')
				->joinWith('store s', false)
				->where(['received' => 1, 'available' => 1])
				->andWhere(['>', 'c.end_time', Timezone::gmtime()])
				->andWhere(['or', ['total' => 0], ['and', ['>', 'total', 0], ['>', 'surplus', 0]]])
				->orderBy(['id' => SORT_DESC]);

			$page = Page::getPage($query->count(), 5, true, $post->page);
			$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
			foreach ($list as $key => $value) {
				$list[$key]['end_time'] = Timezone::localDate('Y-m-d H:i:s', $value['end_time']);
			}

			return Message::result(['list' => $list, 'pagination' => Page::formatPage($page, true, 'basic')]);
		}
	}

	public function actionLimitbuy()
	{
		if (!Yii::$app->request->isPost) {
			$this->params['gcategories'] = GcategoryModel::getOptions(0, -1, null, 2);
			$this->params['page'] = Page::seo(['title' => Language::get('gselector')]);
			return $this->render('../gselector.limitbuy.html', $this->params);
		} else {

			$post = Basewind::trimAll(Yii::$app->request->post(), true);

			$query = LimitbuyModel::find()->alias('lb')
				->select('lb.id,g.goods_id,g.goods_name,g.brand,g.default_image,g.price,g.default_spec as spec_id')
				->joinWith('goods g', false, 'INNER JOIN')
				->joinWith('store s', false)
				->where(['and', ['s.state' => 1, 'g.if_show' => 1, 'g.closed' => 0], ['<=', 'lb.start_time', Timezone::gmtime()], ['>=', 'lb.end_time', Timezone::gmtime()]]);

			if ($post->cate_id) {
				$childIds = GcategoryModel::getDescendantIds($post->cate_id, 0);
				$query->andWhere(['in', 'g.cate_id', $childIds]);
			}
			if ($post->keyword) {
				$query->andWhere(['or', ['like', 'g.goods_name', $post->keyword], ['like', 'g.brand', $post->keyword]]);
			}
			$page = Page::getPage($query->count(), 5, true, $post->page);
			$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();

			$promotool = Promotool::getInstance()->build();
			foreach ($list as $key => $value) {
				if (($promotion = $promotool->getItemProInfo($value['goods_id'], $value['spec_id']))) {
					$promotion['status'] = LimitBuyModel::getLimitbuyStatus($value['id'], true);
					$list[$key]['promotion'] = $promotion;
				}
			}

			return Message::result(['list' => $list, 'pagination' => Page::formatPage($page, true, 'basic')]);
		}
	}

	public function actionTeambuy()
	{
		if (!Yii::$app->request->isPost) {
			$this->params['page'] = Page::seo(['title' => Language::get('gselector')]);
			return $this->render('../gselector.teambuy.html', $this->params);
		} else {

			$post = Basewind::trimAll(Yii::$app->request->post(), true);

			$query = TeambuyModel::find()->alias('tb')->select('tb.id,tb.status,tb.goods_id,tb.people,g.default_image,g.price,g.goods_name,g.default_spec as spec_id')
				->joinWith('goods g', false, 'INNER JOIN')
				->joinWith('store s', false)
				->where(['s.state' => 1, 'g.if_show' => 1, 'g.closed' => 0]);

			$page = Page::getPage($query->count(), 5, true, $post->page);
			$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
			foreach ($list as $key => $value) {
				list($price) = TeambuyModel::getItemProPrice($value['spec_id']);
				$list[$key]['teamPrice'] = $price === false ? $value['price'] : $price;
			}

			return Message::result(['list' => $list, 'pagination' => Page::formatPage($page, true, 'basic')]);
		}
	}

	/**
	 * 查看VUE路由
	 */
	public function actionLink()
	{
		$this->params['page'] = Page::seo(['title' => Language::get('gselector')]);
		return $this->render('../gselector.link.html', $this->params);
	}
}
