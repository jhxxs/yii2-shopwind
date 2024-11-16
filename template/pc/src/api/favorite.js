/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id favorite.js 2022.3.30 $
 * @author mosir
 */

import { request } from '@/common/server.js'
import { ElMessage } from 'element-plus'

/**
 * 获取我收藏的商品
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function favoriteGoods(params, callback, loading) {
    request('favorite/goods', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取我收藏的店铺
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function favoriteStore(params, callback, loading) {
    request('favorite/store', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 用户收藏商品
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function collectGoods(params, callback, loading) {
    request('goods/collect', params, (res) => {
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
 * 用户取消收藏商品
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function uncollectGoods(params, callback, loading) {
    params.remove = true
    request('goods/collect', params, (res) => {
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
 * 用户收藏店铺
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function collectStore(params, callback, loading) {
    request('store/collect', params, (res) => {
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
 * 用户取消收藏店铺
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function uncollectStore(params, callback, loading) {
    params.remove = true
    request('store/collect', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    }, loading)
}