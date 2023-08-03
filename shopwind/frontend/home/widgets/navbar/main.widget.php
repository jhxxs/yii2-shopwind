<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\home\widgets\navbar;

use Yii;

use common\base\BaseWidget;

/**
 * @Id main.widget.php 2018.9.13 $
 * @author mosir
 */

class NavbarWidget extends BaseWidget
{
    var $name = 'navbar';

    public function getData()
    {
        return $this->options;
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
