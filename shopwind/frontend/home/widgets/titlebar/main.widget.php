<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\home\widgets\titlebar;

use Yii;

use common\library\Basewind;
use common\base\BaseWidget;

/**
 * @Id main.widget.php 2018.9.13 $
 * @author mosir
 */

class TitlebarWidget extends BaseWidget
{
    var $name = 'titlebar';

    public function getData()
    {
        return array_merge($this->options, ['baseUrl' => Basewind::homeUrl()]);
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
