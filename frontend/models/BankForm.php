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

use common\models\BankModel;

use common\library\Language;

/**
 * @Id BankForm.php 2018.4.17 $
 * @author mosir
 */
class BankForm extends Model
{
	public $bank;
	public $code;
	public $name;
	public $area;
	public $account;
	public $captcha;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			['code', 'required', 'message' => Language::get('code_empty')],
			['code', 'checkCode'],
			['account', 'required', 'message' => Language::get('account_empty')],
			['name', 'required', 'message' => Language::get('name_error')],
			['name', 'string', 'length' => [2, 120]],
			['area', 'string'],
			['captcha', 'captcha', 'captchaAction' => 'default/captcha', 'message' => Language::get('captcha_failed')],
        ];
    }
	
	/**
     * [scenarios : different validation conditions under different business logic.]
     * @return [type] [description]
     */
    public function scenarios()
    {
        return [
            'default' => ['code', 'name', 'area', 'account', 'captcha'],
			//'add' => [], 
			//'update' = [],
        ];
    }
	
	public function checkCode($attribute, $params)
	{
		if (!$this->hasErrors()) {
			
			$list = self::getBankList();
			if(!$list) $list = array();
			
			$check = false;
			foreach($list as $key => $bank) {
				if(strtoupper($key) == strtoupper($this->code))  {
					$check = true;
					break;
				}
			}
            if ($check == false) {
                $this->addError($attribute, Language::get('code_error'));
            }
        }
	}

    public function save($validate = true)
    {
        if ($validate && !$this->validate()) {
            return false;
        }
		
		$list = self::getBankList();
		foreach($list as $key => $bank) {
			if(strtoupper($key) == strtoupper($this->code))  {
				$this->bank = $bank;
				break;
			}
		}
		// add or edit
		$id = intval(Yii::$app->request->get('id'));
		if(!$id || !($model = BankModel::find()->where(['id' => $id, 'userid' => Yii::$app->user->id])->one())) {
			$model = new BankModel();
		}
		
		$model->userid = Yii::$app->user->id;
		$model->bank = $this->bank;
		$model->code = strtoupper($this->code);
		$model->name = $this->name;
		$model->area = $this->area;
		$model->account = $this->account;
		
		return $model->save(false) ? $model : null;
	}
	
	public static function getBankList()
	{
		return array (
			'ICBC' 		=> '中国工商银行',
			'CCB' 		=> '中国建设银行',
			'ABC' 		=> '中国农业银行',
			'POSTGC' 	=> '中国邮政储蓄银行',
			'COMM' 		=> '交通银行',
			'CMB' 		=> '招商银行',
			'BOC' 		=> '中国银行',
			'CEBBANK' 	=> '中国光大银行',
			'CITIC' 	=> '中信银行',
			'SPABANK' 	=> '深圳发展银行',
			'SPDB' 		=> '上海浦东发展银行',
			'CMBC' 		=> '中国民生银行',
			'CIB' 		=> '兴业银行',
			'GDB' 		=> '广东发展银行',
			'SHRCB'  	=> '上海农村商业银行',
			'SHBANK' 	=> '上海银行',
			'NBBANK' 	=> '宁波银行',
			'HZCB' 		=> '杭州银行',
			'BJBANK'  	=> '北京银行',
			'BJRCB'	  	=> '北京农村商业银行',
			'FDB'	  	=> '富滇银行',
			'WZCB'   	=> '温州银行',
			'CDCB'    	=> '成都银行',
			'HXBANK'	=> '华夏银行',
		);
	}
}
