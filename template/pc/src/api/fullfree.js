/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id fullfree.js 2021.10.30 $
 * @author mosir
 */

import { request } from '@/common/server.js'
import { ElMessage } from 'element-plus'

/**
 * 获取满包邮设置
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function fullfreeRead(params, callback, loading) {
    request('fullfree/read', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            console.log(res)
        }
    }, loading)
}

/**
 * 更新满包邮设置
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function fullfreeUpdate(params, callback, loading) {
    request('fullfree/update', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    }, loading)
}