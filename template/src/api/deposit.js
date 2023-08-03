/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id deposit.js 2021.11.30 $
 * @author mosir
 */

import { request } from '@/common/server.js'
import { ElMessage } from 'element-plus'

/**
 * 获取账户信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function depositRead(params, callback, loading) {
    request('deposit/read', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 更新账户信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function depositUpdate(params, callback, loading) {
    params.verifycodekey = localStorage.getItem('smsverifycodekey')
    request('deposit/update', params, (res) => {
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
 * 获取交易详情
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function depositTrade(params, callback, loading) {
    request('deposit/trade', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取交易记录
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function depositTradeList(params, callback, loading) {
    request('deposit/tradelist', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取收支记录
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function depositRecordList(params, callback, loading) {
    request('deposit/recordlist', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 充值到余额
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function depositRecharge(params, callback, loading) {
    request('deposit/recharge', params, (res) => {
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
 * 余额提现
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function depositDrawal(params, callback, loading) {
    request('deposit/drawal', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    }, loading)
}