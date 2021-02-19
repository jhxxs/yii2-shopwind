<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\models;

use Yii;
use yii\base\Model;

use common\models\DepositTradeModel;
use common\models\CashcardModel;

use common\library\Language;
use common\library\Timezone;
use common\library\Business;

/**
 * @Id DepositCardrechargeForm.php 2018.4.17 $
 * @author mosir
 */
class DepositCardrechargeForm extends Model
{
	public $cardNo;
	public $password;
	public $captcha;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['cardNo', 'password', 'captcha'], 'trim'],
			['cardNo', 'required', 'message' => Language::get('cardNo_empty')],
			['password', 'required', 'message' => Language::get('card_password_empty')],
			['captcha', 'captcha', 'captchaAction' => 'default/captcha', 'message' => Language::get('captcha_failed')],
        ];
    }
	
	public function submit($post = null, $cashcard = null)
	{
		// 转到对应的业务实例，不同的业务实例用不同的文件处理，如购物，卖出商品，充值，提现等，每个业务实例又继承支出或者收入
		$depopay_type = Business::getInstance('depopay')->build(['flow' => 'income', 'type' => 'cardrecharge']);
		$result = $depopay_type->submit(array(
			'trade_info' => array('userid' => Yii::$app->user->id, 'party_id' => 0, 'amount' => $cashcard->money),
			'extra_info' => array('tradeNo' => DepositTradeModel::genTradeNo()),
			'post'		 =>	(object)array('card_id' => $cashcard->id, 'remark' => $cashcard->cardNo),
		));
		if(!$result) {
			$this->errors = $depopay_type->errors;
			return false;
		}
		return true;
	}

    public function validate($attributeNames = null, $clearErrors = true)
    {
        if (!parent::validate($attributeNames, $clearErrors)) {
            return false;
        }
		
		if(!CashcardModel::validateCard($this->cardNo, $this->password)) {
			return $this->addError($attributeNames, Language::get('cashcard_verify_fail'));
		}
		
		$query = CashcardModel::find()->select('active_time,expire_time')->where(['cardNo' => $this->cardNo])->one();
		if($query->active_time > 0) {
			return $this->addError('active_time', Language::get('cashcard_already_used'));
		}
		if(($query->expire_time > 0) && ($query->expire_time <= Timezone::gmtime())) {
			return $this->addError('expire_time', Language::get('cashcard_already_expired'));
		}
		
		return true;
	}
}
