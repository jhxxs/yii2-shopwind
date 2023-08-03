/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id cod.js 2022.4.30 $
 * @author mosir
 */

import { request } from '@/common/server.js'
import { ElMessage } from 'element-plus'

/**
 * 获取指定店铺货到付款记录
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function codRead(params, callback, loading) {
    request('cod/read', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 设置店铺货到付款
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function codUpdate(params, callback, loading) {
    request('cod/update', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            } else {
                ElMessage.warning(res.message)
            }
        }
    }, loading)
}