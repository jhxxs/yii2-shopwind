/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id order.js 2021.11.5 $
 * @author mosir
 */

import { ElMessage } from 'element-plus'
import { request } from '@/common/server.js'

/**
 * 获取订单详情
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function orderRead(params, callback, loading) {
	request('order/read', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 获取订单商品详情
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function orderGoods(params, callback, loading) {
	request('order/goods', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 获取订单发货信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function orderExtm(params, callback, loading) {
	request('order/extm', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 获取订单物流详情
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function orderLogistic(params, callback, loading) {
	request('order/logistic', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 获取订单核销二维码（目前主要针对虚拟商品订单）
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function orderQrcode(params, callback, loading) {
	request('order/qrcode', params, (res) => {
		console.log(res)
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 获取我的订单列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function myOrderList(params, callback, loading) {
	request('my/order/list', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 获取我的订单各状态数量
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading
 */
export function myOrderRemind(params, callback, loading) {
	request('my/order/remind', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 获取卖家订单列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function sellerOrderList(params, callback, loading) {
	request('seller/order/list', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		} else {
			ElMessage.warning(res.message)
		}
	}, loading)
}

/**
 * 获取卖家订单各状态数量
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading
 */
export function sellerOrderRemind(params, callback, loading) {
	request('seller/order/remind', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 更新订单信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function orderUpdate(params, callback, loading) {
	request('order/update', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		} else {
			ElMessage.warning(res.message)
		}
	}, loading)
}

/**
 * 提交订单评价
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function orderEvaluate(params, callback, loading) {
	request('order/evaluate', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		} else {
			ElMessage.warning(res.message)
		}
	}, loading)
}

/**
 * 卖家回复评价
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function orderReplyevaluate(params, callback, loading) {
	request('order/replyevaluate', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		} else {
			ElMessage.warning(res.message)
		}
	}, loading)
}

/**
 * 获取我的订单评价列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function myOrderEvaluates(params, callback, loading) {
	request('my/order/evaluates', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 获取我管理的订单评价列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function sellerOrderEvaluates(params, callback, loading) {
	request('seller/order/evaluates', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 获取预支付订单数据
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function orderBuild(params, callback, loading) {
	request('order/build', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 提交订单
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function orderCreate(params, callback, loading) {
	request('order/create', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		} else {
			ElMessage.warning(res.message)
		}
	}, loading)
}
