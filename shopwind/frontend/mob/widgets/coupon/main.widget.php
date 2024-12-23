<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\mob\widgets\coupon;

use Yii;

use common\models\CouponModel;
use common\library\Timezone;
use common\base\BaseWidget;

/**
 * @Id main.widget.php 2018.9.13 $
 * @author mosir
 */

class CouponWidget extends BaseWidget
{
    var $name = 'coupon';

    public function getData()
    {
        // 初始值
        $this->options = array_merge(
            [
                'source' => '',
                'quantity' => 3,
            ],
            $this->options
        );

        $query = CouponModel::find()->alias('c')->select('c.id,c.name,c.money,c.amount')
            ->where(['received' => 1, 'available' => 1])
            ->andWhere(['>', 'c.end_time', Timezone::gmtime()])
            ->andWhere(['or', ['total' => 0], ['and', ['>', 'total', 0], ['>', 'surplus', 0]]])
            ->orderBy(['id' => SORT_DESC]);

        if ($this->options['source'] == 'choice') {
            $query->andWhere(['in', 'id', explode(',', $this->options['items'])]);
        } else {
            $query->limit($this->options['quantity'] > 0 ? $this->options['quantity'] : 3);
        }
        $list = $query->asArray()->all();

        return array_merge(['list' => $list], $this->options);
    }

    public function parseConfig($input)
    {
        return $input;
    }
}
