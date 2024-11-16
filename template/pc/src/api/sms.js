/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id sms.js 2021.10.25 $
 * @author mosir
 */

import { request } from '@/common/server.js'
import { ElMessage } from 'element-plus'

/**
 * 发送短信
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function smsSend(params, callback) {
    request('sms/send', params, (res) => {
        if (res.code == 0) {
            localStorage.setItem('smsverifycodekey', res.data.codekey)
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    })
}

/**
 * 获取用户短信配置
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function smsRead(params, callback) {
    request('sms/read', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    })
}

/**
 * 更新短信配置
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function smsUpdate(params, callback) {
    request('sms/update', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    })
}

/**
 * 获取短信发送场景
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function smsScene(params, callback) {
    request('sms/scene', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    })
}

