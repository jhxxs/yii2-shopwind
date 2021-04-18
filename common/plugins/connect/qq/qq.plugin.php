<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\connect\qq;

use yii;
use yii\helpers\Url;

use common\library\Language;

use common\plugins\BaseConnect;
use common\plugins\connect\qq\SDK;

/**
 * @Id qq.plugin.php 2018.6.4 $
 * @author mosir
 */

class Qq extends BaseConnect
{
	/**
	 * 插件网关
	 * @var string $gateway
	 */
	protected $gateway = 'https://graph.qq.com/oauth2.0/authorize';

	/**
	 * 插件实例
	 * @var string $code
	 */
	protected $code = 'qq';

	/**
     * SDK实例
	 * @var object $client
     */
	private $client = null;

	/**
	 * 用户编号
	 * @var string $userid
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
		$url = $this->gateway.'?client_id='.$this->config['appId'].'&scope=&redirect_uri='.$this->getReturnUrl().'&state='.mt_rand().'&response_type=code';
		return Yii::$app->getResponse()->redirect($url);
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
			$response->portrait	= $userInfo->figureurl_qq_2;
			$response->nickname	= $userInfo->nickname;
		}
		return $response;
	}
	
	public function getReturnUrl()
	{
		return urlencode(Url::toRoute(['connect/qqcallback'], true));
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