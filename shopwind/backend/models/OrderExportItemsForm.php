<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace backend\models;

use Yii;
use yii\base\Model;

use common\models\RegionModel;
use common\models\GoodsModel;

use common\library\Timezone;
use common\library\Def;

/**
 * @Id OrderExportItemsForm.php 2018.8.2 $
 * @author mosir
 */
class OrderExportItemsForm extends Model
{
	public $errors = null;

	/**
	 * @param $outfile false=浏览器自动下载, true=输出下载文件
	 */
	public static function download($list = [],  $outfile = false, $filename = '')
	{
		// 先处理下数组
		$result = [];
		foreach ($list as $key => $value) {
			foreach ($value['orderGoods'] as $k => $v) {
				$result[] = array_merge($value, ['key' => $k], $v);
			}
		}

		// 文件数组
		$record_xls = array();
		$lang_bill = array(
			'id'			=> '编号',
			'order_sn' 		=> '订单编号',
			'goods_name'	=> '商品名称',
			'specification' => '规格',
			'quantity'		=> '购买数量',
			'barcode'		=> '商品条形码',
			'store_name' 	=> '商家',
			'buyer_name' 	=> '买家',
			'order_amount' 	=> '订单总额',
			// 'payment_name' 	=> '付款方式',
			// 'status'		=> '订单状态',
			// 'add_time' 		=> '下单时间',
			// 'pay_time' 		=> '付款时间',
			// 'ship_time' 	=> '发货时间',
			// 'finished_time'	=> '完成时间',
			'consignee' 	=> '收货人姓名',
			'address' 		=> '收货人地址',
			'phone_mob' 	=> '收货人电话',

		);
		$record_xls[] = array_values($lang_bill);
		$filename = $filename ? $filename : 'ORDER_' . Timezone::localDate('Ymdhis', Timezone::gmtime());

		$amount = $quantity = 0;
		$record_value = array();
		foreach ($result as $key => $value) {
			// 每笔订单标记
			if ($value['key'] == 0) {
				$quantity++;
				$amount += floatval($value['order_amount']);
			}

			foreach ($lang_bill as $k => $v) {
				if ($value['key'] > 0 && !in_array($k, ['id', 'goods_name', 'specification', 'quantity', 'barcode'])) {
					$record_value[$k] = '';
					continue;
				}

				if ($k == 'id') {
					$value[$k] = $key + 1;
				}
				if ($k == 'order_sn') {
					$value[$k] = '\'' . $value[$k];
				}
				if (in_array($k, ['add_time', 'pay_time', 'ship_time', 'finished_time'])) {
					$value[$k] = Timezone::localDate('Y/m/d H:i:s', $value[$k]);
				}
				if ($k == 'address') {
					if ($array = RegionModel::getArray($value['region_id'])) {
						$value[$k] = implode('', $array) . $value[$k];
					}
				}
				if ($k == 'status') {
					$value[$k] = Def::getOrderStatus($value['status']);
				}

				$record_value[$k] = $value[$k] ? $value[$k] : '';
			}

			$record_xls[] = $record_value;
		}

		$record_xls[] = array('id' => ''); // empty line
		$record_xls[] = array('id' => sprintf('订单总数：%s笔，订单总金额：%s元', $quantity, $amount));

		return \common\library\Page::export([
			'models' 	=> $record_xls,
			'filename' 	=> $filename,
		], $outfile);
	}
}
