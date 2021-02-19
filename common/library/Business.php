<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\library;

use yii;

/**
 * @Id Business.php 2018.3.9 $
 * @author mosir
 */
 
class Business
{
	public $instance = 'order';
	
	public function __construct($options = null)
	{
		if($options !== null) {
			if(is_string($options)) $options = ['instance' => $options];
			foreach($options as $key => $val) {
				$this->$key = $val;
			}
		}
	}
	
	public static function getInstance($options = null)
	{
		return new Business($options);
	}
	
	public function build($params = array())
	{
		if(in_array($this->instance, ['order'])) {
			return $this->ot($params);
		}
		if(in_array($this->instance, ['depopay'])) {
			return $this->dpt($params);
		}
		return null;
	}
	
	private function ot($params = array())
	{
		extract($params);
		
		static $order_type = array();
    	$hash = md5(var_export($params, true));
    	if ((isset($is_new) && $is_new) || empty($order_type) || !isset($order_type[$hash]))
    	{
			// 订单业务基础类
			$base_file = Yii::getAlias('@common') . '/business/BaseOrder.php';
			$type_file = Yii::getAlias('@common') . '/business/ordertypes/normal.otype.php';
			if($type != 'normal' && is_file($type_file)) {
				include_once($type_file);
				$type_file = Yii::getAlias('@common') . '/business/ordertypes/'.$type.'.otype.php';
			}
			if(!is_file($base_file) || !is_file($type_file)) {
				return false;
			}
		
			include_once($base_file);
			include_once($type_file);
		
			$class_name = sprintf("common\business\ordertypes\%s", ucfirst($type).ucfirst($this->instance));
		
			$order_type[$hash] = new $class_name($params);
		}
		return $order_type[$hash];
	}
	
	/**
	 * @var flow string income|outlay
	 * @var is_new use new object
	 * @return depopay object
	 */
	private function dpt($params = array())
	{
		extract($params);
		
		static $depopay_type = array();
    	$hash = md5(var_export($params, true));
    	if ((isset($is_new) && $is_new) || empty($depopay_type) || !isset($depopay_type[$hash]))
    	{
			// 支付业务基础类
			$base_file = Yii::getAlias('@common') . '/business/BaseDepopay.php';
			$flow_file = Yii::getAlias('@common') . '/business/depopaytypes/'. $flow .'.depopay.php';
			$type_file = Yii::getAlias('@common') . '/business/depopaytypes/'.$type . '.'.$flow.'.php';
			if(!is_file($base_file) || !is_file($flow_file) || !is_file($type_file)) {
				return false;
			}
		
			include_once($base_file);
			include_once($flow_file);
			include_once($type_file);
			
			$class_name = sprintf("common\business\depopaytypes\%s", ucfirst($type).ucfirst($flow));
		
			$depopay_type[$hash] = new $class_name($params);
		}
		return $depopay_type[$hash];
	}
}