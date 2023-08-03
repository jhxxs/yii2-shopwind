/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id cart.js 2022.5.30 $
 * @author mosir
 */

import { request } from '@/common/server.js'
import { redirect, getUrl } from '@/common/util.js'
import { ElMessage } from 'element-plus'

/**
 * 获取购物车商品（包含多个店铺的商品）
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function cartList(params, callback, loading) {
    request('cart/list', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 添加商品到购物车
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function cartAdd(params, callback, loading) {
    request('cart/add', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else if (res.code == 4004) {
            redirect('/user/login?redirect=' + encodeURIComponent(getUrl()))
        } else {
            ElMessage.warning(res.message)
        }
    }, loading)
}

/**
 * 更新购物车商品
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function cartUpdate(params, callback, loading) {
    request('cart/update', params, (res) => {
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
 * 清空购物车
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function cartDelete(params, callback, loading) {
    request('cart/delete', params, (res) => {
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
 * 删除购物车商品
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function cartRemove(params, callback, loading) {
    request('cart/remove', params, (res) => {
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
 * 选择商品以结算
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function cartChose(params, callback, loading) {
    request('cart/chose', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    }, loading)
}

