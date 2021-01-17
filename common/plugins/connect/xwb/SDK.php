<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */

namespace common\plugins\connect\xwb;

use yii;

use common\plugins\connect\xwb\lib\SaeTOAuthV2;

/**
 * @Id SDK.php 2018.6.5 $
 * @author mosir
 */

class SDK
{
	/**
	 * 插件网关
	 * @param string $gateway
	 */
	protected $gateway = null;

	/**
	 * @param string $WB_AKEY
	 */
	public $WB_AKEY = null;

	/**
	 * @param string $WB_SKEY
	 */
	public $WB_SKEY;

	/**
	 * 返回地址
	 * @param string $redirect_uri
	 */
	public $redirect_uri = null;
	
	/**
	 * 构造函数
	 */
	public function __construct(array $config)
	{
		foreach($config as $key => $value) {
            $this->$key = $value;
        }
	}
	
	public function getAccessToken($auth_code = '')
	{
		$response = false;
		
		if($auth_code) 
		{
			$o = new SaeTOAuthV2($this->WB_AKEY, $this->WB_SKEY);
			$token = $o->getAccessToken('code', array('code' => $auth_code, 'redirect_uri' => $this->redirect_uri));
			
			$response = (object)array_merge($token, ['unionid' => $token['access_token']]);
		}
		return $response;
	}
	
	public function getUserInfo($resp = null)
	{
		return false;
	}
	
	public function getAuthorizeURL()
	{
		$o = new SaeTOAuthV2($this->WB_AKEY, $this->WB_SKEY);
		return $o->getAuthorizeURL($this->redirect_uri);
	}
}