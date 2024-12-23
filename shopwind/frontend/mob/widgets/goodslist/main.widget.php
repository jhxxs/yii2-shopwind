<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\mob\widgets\goodslist;

use Yii;

use common\models\GoodsModel;
use common\models\GcategoryModel;

use common\base\BaseWidget;

/**
 * @Id main.widget.php 2018.9.13 $
 * @author mosir
 */

class GoodslistWidget extends BaseWidget
{
    var $name = 'goodslist';

    public function getData()
    {
        // 初始值
        $this->options = array_merge(
            [
                'source' => '',
                'paging' => 0,
                'quantity' => 4,
                'orderby' => ''
            ],
            $this->options
        );
        
        $query = GoodsModel::find()->alias('g')->select('g.goods_id,goods_name,price,mkprice,default_image,gst.sales')
            ->joinWith('goodsStatistics gst', false)
            ->where(['if_show' => 1, 'closed' => 0]);
       
        if($this->options['source'] == 'choice') {
            $items = explode(',', $this->options['items']);
            $query->andWhere(['in', 'g.goods_id', $items]);
            $this->options['quantity'] = count($items);
        } else {
            if($this->options['source'] == 'category') {
                $query->andWhere(['in', 'cate_id', GcategoryModel::getDescendantIds($this->options['cate_id'])]);
            }
        }
        if($this->options['paging'] == 1) {
            $query->limit($this->options['page_size'] > 0 ? $this->options['page_size'] : 4);
        } else {
            $query->limit($this->options['quantity'] > 0 ? $this->options['quantity'] : 4);
        }

        if ($this->options['orderby']) {
            $orderBy = explode('|', $this->options['orderby']);
            $query->orderBy([(in_array($orderBy[0], ['add_time', 'price']) ? 'g.' . $orderBy[0] : $orderBy[0]) => strtolower($orderBy[1]) == 'asc' ? SORT_ASC : SORT_DESC, 'g.goods_id' => SORT_DESC]);
        } else $query->orderBy(['g.recommended' => SORT_DESC, 'gst.sales' => SORT_DESC, 'g.goods_id' => SORT_DESC]);

        if (empty($list = $query->asArray()->all())) {
            $list = array([], [], [], []);
        }

        return array_merge(['list' => $list], $this->options);
    }

    public function parseConfig($input)
    {
        return $input;
    }   
}
