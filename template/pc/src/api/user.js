/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id user.js 2020.10.25 $
 * @author mosir
 */

import { ElMessageBox, ElMessage } from 'element-plus'
import { request } from '@/common/server.js'
import { redirect } from '@/common/util.js'

/**
 * 获取用户信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function userRead(params, callback, loading) {
    request('user/read', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 更新用户信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function userUpdate(params, callback, loading) {
    request('user/update', params, (res) => {
        if (res.code == 0) {
            let visitor = JSON.parse(localStorage.getItem('visitor'))
            localStorage.setItem('visitor', JSON.stringify(Object.assign(visitor, { portrait: res.data.portrait })));

            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    }, loading)
}

/**
 * 更新用户登录密码
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function userEditPassword(params, callback, loading) {
    params.verifycodekey = localStorage.getItem('smsverifycodekey')
    request('user/password', params, (res) => {
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
 * 更新用户手机号
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function userEditPhone(params, callback, loading) {
    params.verifycodekey = localStorage.getItem('smsverifycodekey')
    request('user/phone', params, (res) => {
        if (res.code == 0) {
            let visitor = JSON.parse(localStorage.getItem('visitor'))
            localStorage.setItem('visitor', JSON.stringify(Object.assign(visitor, { phone_mob: res.data.phone_mob })));

            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    }, loading)
}

/**
 * 用户登录获取TOKEN
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function userLogin(params, callback, loading) {
    params.terminal = 'PC'
    request('auth/login', params, (res) => {
        if (res.code == 0 && res.data) {
            if (res.data.user_info) {
                if (params.requiresSeller && !res.data.user_info.store_id) {
                    return ElMessageBox({
                        title: '提示',
                        message: '您还不是卖家',
                        showCancelButton: false,
                        type: 'warning'
                    })
                }

                localStorage.setItem('access_token', res.data.token)
                localStorage.setItem('visitor', JSON.stringify(res.data.user_info))

                if (typeof callback == 'function') {
                    callback(res.data)
                }
            } else {
                ElMessage.warning('登录失败')
            }
        } else {
            ElMessageBox({
                title: '提示',
                message: res.message ? res.message : '未知错误',
                showCancelButton: false,
                type: 'warning'
            })
        }
    }, loading)
}

/**
 * 用户退出
 * @param {Function} callback 
 */
export function userLogout(callback) {
    localStorage.removeItem('access_token')
    localStorage.removeItem('visitor')
    if (typeof callback == 'function') {
        callback()
    } else {
        redirect('/user/login')
    }
}

/**
 * 联合账号登录跳转授权
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function userConnect(params, callback, loading) {
    params.terminal = 'PC'
    request('auth/connect', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessageBox({
                title: '提示',
                message: res.message ? res.message : '未知错误',
                showCancelButton: false,
                type: 'warning'
            })
        }
    }, loading)
}