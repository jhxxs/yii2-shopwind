<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\mob\controllers;

use Yii;

use common\library\Language;
use common\library\Page;

/**
 * @Id DefaultController.php 2018.9.13 $
 * @author mosir
 */

class DefaultController extends \common\base\BaseController
{
    public function actionIndex()
    {
		$this->params['page'] = Page::seo(['title' => Language::get('home')]);
		return $this->render('../diy.index.html', $this->params);
    }
}