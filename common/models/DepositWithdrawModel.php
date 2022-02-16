<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

use common\models\DepositTradeModel;
use common\models\DepositAccountModel;

use common\library\Timezone;
use common\library\Language;
use common\library\Plugin;

/**
 * @Id DepositWithdrawModel.php 2018.4.3 $
 * @author mosir
 */

class DepositWithdrawModel extends ActiveRecord
{
	public $errors;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%deposit_withdraw}}';
    }
	
	// 关联表
	public function getDepositTrade()
	{
		return parent::hasOne(DepositTradeModel::className(), ['bizOrderId' => 'orderId']);
	}
	// 关联表
	public function getUser()
	{
		return parent::hasOne(UserModel::className(), ['userid' => 'userid']);
	}

	/**
	 * 处理提现的资金及改变交易状态
	 * @var string $tradeNo 交易号
	 * @var string $method 转账方式，取值：manual（人工转账）、online（在线转账，目前仅支持支付宝） 
	 */
	public function handleMoney($tradeNo, $method = 'manual')
	{
		// 变更交易状态
		if(($model = DepositTradeModel::find()->where(['tradeNo' => $tradeNo])->one())) {

			if(empty($model->payTradeNo)) {
				$model->payTradeNo = DepositTradeModel::genPayTradeNo();
			}
			
			if($method == 'online') {
				$payee = parent::find()->select('name,account')->where(['orderId' => $model->bizOrderId])->asArray()->one();
				if(!($alipayOrderId = $this->alipay($model->payTradeNo, $model->amount, $payee, $model->title))) {
					return false;
				}

				// 保存支付宝交易号
				$model->outTradeNo = $alipayOrderId;
			}

			$model->status = 'SUCCESS';
			$model->end_time = Timezone::gmtime();
			if(!$model->save()) {
				$this->errors = $model->errors;
				return false;
			}

			// 扣减当前用户的冻结金额
			if(DepositAccountModel::updateDepositFrozen($model->buyer_id, $model->amount, 'reduce') !== false) {
				// TODO...

				return true;
			}
		}

		return false;
	}

	/**
	 * 通过支付宝接口自动转账
	 * 款项从平台企业支付宝划拨到提现者支付宝，请注意开启该接口时候，确保后台登录安全
	 */
	private function alipay($payTradeNo, $money, $payee, $title)
	{
		$payment = Plugin::getInstance('payment')->build('alitranpay');
		if(!($payment_info = $payment->getInfo()) || !$payment_info['enabled']) {
			$this->errors = Language::get('interface_disabled');
			return false;
		}
		
		$params['amount'] = $money;
		$params['payTradeNo'] = $payTradeNo;
		$params['title'] = $title;
		$params['payee'] = $payee;
		
		if(!($result = $payment->transfer($params))) {
			$this->errors = $payment->errors ? $payment->errors : Language::get('transfer_fail');
			return false;
		}

		return $result;
	}
}
