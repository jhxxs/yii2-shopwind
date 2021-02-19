<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\connect\alipay;

use yii;
use yii\helpers\Url;

use common\library\Basewind;
use common\library\Language;

use common\plugins\BaseConnect;
use common\plugins\connect\alipay\SDK;

/**
 * @Id alipay.plugin.php 2018.6.3 $
 * @author mosir
 */

class Alipay extends BaseConnect
{
	/**
	 * 插件网关
	 * @var string $gateway
	 */
	protected $gateway = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm';

	/**
	 * 插件实例
	 * @var string $code
	 */
	protected $code = 'alipay';

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

		// 更换为移动端的秘钥（因为授权域名不同，需要用不同的秘钥）
		$fields = ['appId', 'rsaPublicKey', 'rsaPrivateKey', 'alipayrsaPublicKey', 'signType'];
		if(Basewind::getCurrentApp() == 'wap') 
		{
			foreach($fields as $key) {
				$this->config[$key] = $this->config[$key.'_wap'];
				unset($this->config[$key.'_wap']);
			}
			
		} else {
			foreach($fields as $key) {
				unset($this->config[$key.'_wap']);
			}
		}
	}
	
	public function login()
	{
		$url = $this->gateway.'?app_id='.$this->config['appId'].'&scope=auth_user&redirect_uri='.$this->getReturnUrl().'&state='.mt_rand();
		if(Basewind::getCurrentApp() == 'wap') {
			$url = 'alipays://platformapi/startapp?appId=20000067&url='.urlencode($url);
		}
		return Yii::$app->controller->redirect($url);
	}
	
	public function callback($get, $post)
	{
		if((($response = $this->getClient()->getAccessToken($get->auth_code)) == false) || !$response->access_token) {
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
			$response->portrait	= $userInfo->avatar;
			$response->nickname = $userInfo->nick_name;
		}
		return $response;
	}
	
	public function getReturnUrl()
	{
		return urlencode(Url::toRoute(['connect/alipaycallback'], true));
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