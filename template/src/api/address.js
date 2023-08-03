/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id address.js 2021.10.30 $
 * @author mosir
 */

import { request } from '@/common/server.js'
import { ElMessage } from 'element-plus'

/**
 * 获取地址列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function addressList(params, callback, loading) {
    request('address/list', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取指定地址信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function addressRead(params, callback, loading) {
    request('address/read', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 添加收货地址
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function addressAdd(params, callback, loading) {
    request('address/add', params, (res) => {
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
 * 更新收货地址
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function addressUpdate(params, callback, loading) {
    request('address/update', params, (res) => {
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
 * 删除收货地址
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function addressDelete(params, callback, loading) {
    request('address/delete', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    }, loading)
}