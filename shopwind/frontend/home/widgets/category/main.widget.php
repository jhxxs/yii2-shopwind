<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\home\widgets\category;

use Yii;

use common\models\GcategoryModel;

use common\base\BaseWidget;

/**
 * @Id main.widget.php 2018.9.13 $
 * @author mosir
 */

class CategoryWidget extends BaseWidget
{
    var $name = 'category';

    public function getData()
    {
        $parentid = intval($this->options['cate_id']);
        $list = GcategoryModel::getList($parentid, 0, true, $this->options['rows'] ? $this->options['rows'] : 7, 'cate_id,cate_name');
        foreach ($list as $key => $value) {
            $list[$key]['children'] = GcategoryModel::getList($value['cate_id'], 0, true, 6, 'cate_id,cate_name');
        }

        return array_merge(['list' => $list], $this->options);
    }

    public function parseConfig($input)
    {
        $result = [];

        $num = isset($input['title']) ? count($input['title']) : 0;
        if ($num > 0) {
            for ($i = 0; $i < $num; $i++) {
                if (!empty($input['title'][$i])) {
                    $result[] = array(
                        'title'   => $input['title'][$i],
                        'link'  => $input['link'][$i]
                    );
                }
            }
        }
        unset($input['title'], $input['link']);

        return array_merge(['navs' => $result], $input);
    }
}
