/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id webim.js 2022.11.25 $
 * @author mosir
 */

import { request } from '@/common/server.js'
import { ElMessage } from 'element-plus'

/**
 * 获取客服列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function webimList(params, callback, loading) {
    request('webim/list', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取客服聊天记录
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function webimLogs(params, callback, loading) {
    request('webim/logs', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 发送会话
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function webimSend(params, callback, loading) {
    request('webim/send', params, (res) => {
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
 * 获取未读消息数
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function webimUnread(params, callback, loading) {
    request('webim/unread', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}