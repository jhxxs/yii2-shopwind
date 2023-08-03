<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\home\widgets\textbox;

use Yii;

use common\base\BaseWidget;

/**
 * @Id main.widget.php 2018.9.13 $
 * @author mosir
 */

class TextboxWidget extends BaseWidget
{
    var $name = 'textbox';

    public function getData()
    {
        return $this->options;
    }

    public function parseConfig($input)
    {
        return $input;
    }   
}
