<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\home\widgets\swiper;

use Yii;

use common\base\BaseWidget;

/**
 * @Id main.widget.php 2018.9.13 $
 * @author mosir
 */

class SwiperWidget extends BaseWidget
{
    var $name = 'swiper';

    public function getData()
    {
        return $this->options;
    }

    public function parseConfig($input)
    {
        $result = [];

        $index = intval($input['index']);
        $num = isset($input['ad_link_url']) ? count($input['ad_link_url']) : 0;

        if ($num > 0) {
            for ($i = 0; $i < $num; $i++) {
                if ($index == $i && ($image = $this->upload("ad_image_file[0]"))) {
                    $input['ad_image_url'][$i] = $image;
                }

                if (!empty($input['ad_image_url'][$i])) {
                    $result[] = array(
                        'url' => $input['ad_image_url'][$i],
                        'link'  => $input['ad_link_url'][$i]
                    );
                }
            }
        }
        unset($input['ad_image_url'], $input['ad_link_url'], $input['ad_image_file'], $input['index']);

        return array_merge(['images' => $result], $input);
    }
}
