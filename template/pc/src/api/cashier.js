/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id cashier.js 2022.4.30 $
 * @author mosir
 */

import { request } from '@/common/server.js'
import { siteUrl } from '@/common/util.js'
import { ElMessage } from 'element-plus'

/**
 * 获取收银台订单支付数据
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function cashierBuild(params, callback, loading) {
    request('cashier/build', params, (res) => {
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
 * 支付订单
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function cashierPay(params, callback, loading) {
    request('cashier/pay', params, (res) => {
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
 * 查询支付交易状态
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function cashierCheckpay(params, callback, loading) {
    request('cashier/checkpay', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}