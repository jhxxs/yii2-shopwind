<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright ( c ) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes.
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\api\controllers\seller;

use Yii;

use common\models\GoodsSpecModel;
use common\models\TeambuyModel;

use common\library\Basewind;
use common\library\Page;
use common\library\Promotool;

use frontend\api\library\Formatter;
use frontend\api\library\Respond;

/**
 * @Id WholesaleController.php 2021.11.8 $
 * @author yxyc
 */

class WholesaleController extends \common\base\BaseApiController
{
    /**
     * 获取卖家管理的批发商品列表
     * @api 接口访问地址: http://api.xxx.com/seller/wholesale/list
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

        $page = array('pageSize' => $post->page_size ? $post->page_size : 10);
        $wholesaleTool = Promotool::getInstance('wholesale')->build(['store_id' => Yii::$app->user->id]);
        if (($message = $wholesaleTool->checkAvailable()) !== true) {
            return $respond->output(Respond::PARAMS_INVALID, $message);
        }

        $params = ['and'];
        if ($post->keyword) {
            $params[] = ['like', 'goods_name', $post->keyword];
        }
        if ($post->status) {
            $params[] = ['status' => $post->status == 'going' ?  1 : 0];
        }

        if (($list = $wholesaleTool->getList($params, $page))) {
            foreach ($list as $key => $value) {
                $list[$key]['goods_image'] = Formatter::path($value['goods_image'], 'goods');
            }
        }

        return $respond->output(true, null, ['list' => $list, 'pagination' => Page::formatPage($page, false)]);
    }
}
