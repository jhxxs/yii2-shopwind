<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\base;

use Yii;

/**
 * @Id BaseApiController.php 2018.10.20 $
 * @author mosir
 */

class BaseApiController extends BaseController
{
	/**
	 * 不启用布局
	 */
	public $layout = false;

	/**
	 * 关闭CSRF验证
	 */
	public $enableCsrfValidation = false;

	/**
	 * 初始化
	 * @var array $params 传递给视图的公共参数
	 */
	public function init()
	{
		parent::init();
		$this->params = [];
	}
}
