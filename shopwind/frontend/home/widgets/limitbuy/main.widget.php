<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\home\widgets\limitbuy;

use Yii;

use common\models\LimitbuyModel;

use common\library\Timezone;
use common\library\Promotool;

use common\base\BaseWidget;

/**
 * @Id main.widget.php 2018.9.13 $
 * @author mosir
 */

class LimitbuyWidget extends BaseWidget
{
    var $name = 'limitbuy';

    public function getData()
    {
        $query = LimitbuyModel::find()->alias('lb')->select('g.goods_id,g.goods_name,g.default_image,g.price,g.default_spec as spec_id')
            ->joinWith('goods g', false, 'INNER JOIN')
            ->joinWith('store s', false)
            ->where(['and', ['s.state' => 1, 'g.if_show' => 1, 'g.closed' => 0], ['<=', 'lb.start_time', Timezone::gmtime()], ['>=', 'lb.end_time', Timezone::gmtime()]])
            ->orderBy(['id' => SORT_DESC]);

        if ($this->options['source'] == 'choice') {
            $query->andWhere(['in', 'g.goods_id', explode(',', $this->options['items'])]);
        } else {
            $query->limit($this->options['quantity'] > 0 ? $this->options['quantity'] : 5);
        }

        if (empty($list = $query->asArray()->all())) {
            $list = array([], [], [], [], []);
        }

        $promotool = Promotool::getInstance()->build();
        foreach ($list as $key => $value) {
            $list[$key]['promotion'] = $promotool->getItemProInfo($value['goods_id'], $value['spec_id']);
        }

        return array_merge(['list' => $list], $this->options);
    }

    public function parseConfig($input)
    {
        if ($image = $this->upload("file")) {
            $input['image'] = $image;
        }
        unset($input['file']);

        return $input;
    }
}
