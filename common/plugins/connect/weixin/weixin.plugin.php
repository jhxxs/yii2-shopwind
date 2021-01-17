<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */

namespace common\plugins\connect\weixin;

use yii;
use yii\helpers\Url;

use common\library\Language;

use common\plugins\BaseConnect;
use common\plugins\connect\weixin\SDK;

/**
 * @Id weixin.plugin.php 2018.6.6 $
 * @author mosir
 */

class Weixin extends BaseConnect
{
	/**
	 * 插件网关
	 * @var string $gateway
	 */
	protected $gateway = 'https://open.weixin.qq.com/connect/qrconnect';
	
	/**
	 * 插件实例
	 * @var string $code
	 */
	protected $code = 'weixin';
	
	/**
     * SDK实例
	 * @var object $client
     */
	private $client = null;

	/**
	 * 用户编号
	 * @var int $userid
	 */
	public $userid;
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->config['redirect_uri'] = $this->getReturnUrl();
	}
	
	public function login()
	{
		return Yii::$app->controller->redirect($this->getClient()->getAuthorizeURL());
	}
	
	public function callback($get, $post)
	{
		if((($response = $this->getClient()->getAccessToken($get->code)) == false) || !$response->access_token) {
			$this->errors = Language::get('get_access_token_fail');
			return false;
		}
		if(!$response->unionid) {
			$this->errors = Language::get('unionid_empty');
			return false;
		}
		$response->code = $this->code;
		
		if(($userid = parent::isBind($response->unionid, $this->code)) === false) {
			return parent::goBind($this->getUserInfo($response));
		} 
		elseif($this->errors) {
			return false;
		}
		$this->userid = $userid;
		
		return true;
	}
	
	public function getUserInfo($response = null)
	{
		if(($userInfo = $this->getClient()->getUserInfo($response)) != false) {
			$response->portrait	= $userInfo->headimgurl;
			$response->nickname = $userInfo->nickname;
		}
		return $response;
	}
	
	public function getReturnUrl()
	{
		// 注：如果未开启URL美化功能就有问题，留待日后处理
		return urlencode(Url::toRoute(['connect/weixincallback'], true));
	}

	/**
     * 获取SDK实例
     */
    public function getClient()
    {
        if($this->client === null) {
            $this->client = new SDK($this->config);
        }
        return $this->client;
    }
}