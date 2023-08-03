<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\home\widgets\goodslist;

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

        $query = GoodsModel::find()->alias('g')->select('g.goods_id,goods_name,price,mkPrice,default_image,gst.sales')
            ->joinWith('goodsStatistics gst', false)
            ->where(['if_show' => 1, 'closed' => 0]);

        if ($this->options['source'] == 'choice') {
            $items = explode(',', $this->options['items']);
            $query->andWhere(['in', 'g.goods_id', $items]);
            $this->options['quantity'] = count($items);
        } else {
            if ($this->options['source'] == 'category') {
                $query->andWhere(['in', 'cate_id', GcategoryModel::getDescendantIds($this->options['cate_id'])]);
            }
        }
        if ($this->options['paging'] == 1) {
            $query->limit($this->options['page_size'] > 0 ? $this->options['page_size'] : 5);
        } else {
            $query->limit($this->options['quantity'] > 0 ? $this->options['quantity'] : 5);
        }

        if ($this->options['orderby']) {
            $orderBy = explode('|', $this->options['orderby']);
            $query->orderBy([$orderBy[0]  => $orderBy[1] == 'desc' ? SORT_DESC : SORT_ASC]);
        } else {
            $query->orderBy(['g.goods_id' => SORT_DESC]);
        }

        if (empty($list = $query->asArray()->all())) {
            $list = array([], [], [], [], [], [], [], []);
        }

        return array_merge(['list' => $list], $this->options);
    }

    public function parseConfig($input)
    {
        $result = [];

        $index = intval($input['index']);
        $num = isset($input['link']) ? count($input['link']) : 0;
        if ($num > 0) {
            for ($i = 0; $i < $num; $i++) {
                if ($index == $i && ($image = $this->upload("file[0]"))) {
                    $input['url'][$i] = $image;
                }

                if (!empty($input['url'][$i])) {
                    $result[] = array(
                        'url'   => $input['url'][$i],
                        'link'  => $input['link'][$i]
                    );
                }
            }
        }
        unset($input['url'], $input['link'], $input['file'], $input['index']);

        return array_merge(['images' => $result], $input);
    }
}
