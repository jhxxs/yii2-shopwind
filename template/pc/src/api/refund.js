/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id refund.js 2021.12.8 $
 * @author mosir
 */

import { ElMessage } from 'element-plus'
import { request } from '@/common/server.js'

/**
 * 获取退款详情
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function refundRead(params, callback, loading) {
	request('refund/read', params, (res) => {
		if (res.code == 0) {
			if (res.data.shipped) {
				res.data.shipped = parseInt(res.data.shipped) // for el-select
			}
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 获取退款留言记录
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function refundLogs(params, callback, loading) {
	request('refund/logs', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 获取我的退款列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function myRefundList(params, callback, loading) {
	request('my/refund/list', params, (res) => {
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		}
	}, loading)
}

/**
 * 获取卖家退款订单列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function sellerRefundList(params, callback, loading) {
	request('seller/refund/list', params, (res) => {
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
 * 创建退款
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function refundCreate(params, callback, loading) {
	request('refund/create', params, (res) => {
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
 * 更新退款信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function refundUpdate(params, callback, loading) {
	request('refund/update', params, (res) => {
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
 * 买家取消退款
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function refundCancel(params, callback, loading) {
	request('refund/cancel', params, (res) => {
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
 * 卖家拒绝退款
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function refundRefuse(params, callback, loading) {
	request('refund/refuse', params, (res) => {
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
 * 卖家同意退款
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function refundAgree(params, callback, loading) {
	request('refund/agree', params, (res) => {
		console.log(res)
		if (res.code == 0) {
			if (typeof callback == 'function') {
				callback(res.data)
			}
		} else {
			ElMessage.warning(res.message)
		}
	}, loading)
}

