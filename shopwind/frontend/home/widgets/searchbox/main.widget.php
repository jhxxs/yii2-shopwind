<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\home\widgets\searchbox;

use Yii;

use common\base\BaseWidget;

/**
 * @Id main.widget.php 2018.9.13 $
 * @author mosir
 */

class SearchboxWidget extends BaseWidget
{
    var $name = 'searchbox';

    public function getData()
    {
        $this->options['keywords'] = explode(',', Yii::$app->params['hot_keywords']);
        $this->options['site_logo'] = Yii::$app->params['site_logo'];
        return $this->options;
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
