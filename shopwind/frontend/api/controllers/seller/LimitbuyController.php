<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes.
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\api\controllers\seller;

use Yii;

use common\models\LimitbuyModel;

use common\library\Basewind;
use common\library\Page;
use common\library\Promotool;
use common\library\Timezone;

use frontend\api\library\Formatter;
use frontend\api\library\Respond;

/**
 * @Id LimitbuyController.php 2018.12.8 $
 * @author yxyc
 */

class LimitbuyController extends \common\base\BaseApiController
{
    /**
     * 获取卖家管理的秒杀商品列表
     * @api 接口访问地址: https://www.xxx.com/api/seller/limitbuy/list
     */
    public function actionList()
    {
        // 验证签名
        $respond = new Respond();
        if (!$respond->verify(true)) {
            return $respond->output(false);
        }

        // 业务参数
        $post = Basewind::trimAll($respond->getParams(), true, ['page', 'page_size']);

        $query = LimitbuyModel::find()->alias('lb')->select('lb.id,lb.goods_id,lb.start_time,lb.end_time,lb.image,g.default_image as goods_image,g.price,g.goods_name,g.default_spec as spec_id,g.if_show,g.closed')
            ->joinWith('goods g', false, 'INNER JOIN')
            ->where(['lb.store_id' => Yii::$app->user->id])
            ->orderBy(['id' => SORT_DESC]);

        if ($post->status == 'going') {
            $query->andWhere(['and', ['g.if_show' => 1, 'g.closed' => 0], ['<', 'lb.start_time', Timezone::gmtime()], ['>', 'lb.end_time', Timezone::gmtime()]]);
        } elseif ($post->status == 'ended') {
            $query->andWhere(['<', 'lb.end_time', Timezone::gmtime()]);
        } elseif ($post->status == 'waiting') {
            $query->andWhere(['and', ['g.if_show' => 1, 'g.closed' => 0], ['>', 'lb.start_time', Timezone::gmtime()]]);
        } elseif ($post->status == 'invalid') {
            $query->andWhere(['or', ['g.if_show' => 0], ['g.closed' => 1]]);
        }

        // 获取指定的时间段
        if ($post->begin) {
            $query->andWhere(['>=', 'lb.start_time', Timezone::gmstr2time($post->begin)]);
        }
        if ($post->end) {
            $query->andWhere(['<=', 'lb.end_time', Timezone::gmstr2time($post->end)]);
        }

        if ($post->keyword) {
            $query->andWhere(['or', ['like', 'title', $post->keyword], ['like', 'goods_name', $post->keyword]]);
        }

        $page = Page::getPage($query->count(), $post->page_size, false, $post->page);
        $list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();

        $promotool = Promotool::getInstance()->build();
        foreach ($list as $key => $value) {
            $promotion = $promotool->getItemProInfo($value['goods_id'], $value['spec_id'], false);
            $list[$key]['promotion'] = array_merge($promotion ? $promotion : [], LimitBuyModel::getSpeedOfProgress($value['id'], $value['goods_id'], false));

            $list[$key]['goods_image'] = Formatter::path($value['goods_image'], 'goods');
            $list[$key]['image'] = Formatter::path($value['image']);
            $list[$key]['status'] = LimitBuyModel::getLimitbuyStatus($value['id'], true);
            if (!$value['if_show'] || $value['closed']) {
                $list[$key]['status'] = 'invalid';
            }
            $list[$key]['start_time'] = Timezone::localDate('Y-m-d H:i:s', $value['start_time']);
            $list[$key]['end_time'] = Timezone::localDate('Y-m-d H:i:s', $value['end_time']);
        }

        return $respond->output(true, null, ['list' => $list, 'pagination' => Page::formatPage($page, false)]);
    }
}
