/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id category.js 2022.4.30 $
 * @author mosir
 */

import { request } from '@/common/server.js'
import { ElMessage } from 'element-plus'

/**
 * 获取类目列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function categoryList(params, callback, loading) {
    request('category/list', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取类目列表(树型结构)
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function categoryTree(params, callback, loading) {
    request('category/tree', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取类目信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function categoryRead(params, callback, loading) {
    request('category/read', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 新增商品类目（店铺）
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function categoryAdd(params, callback, loading) {
    request('category/add', params, (res) => {
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
 * 更新商品类目（店铺）
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function categoryUpdate(params, callback, loading) {
    request('category/update', params, (res) => {
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
 * 删除商品类目（店铺）
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function categoryDelete(params, callback, loading) {
    request('category/delete', params, (res) => {
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
 * 获取类目属性列表信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function categoryAttributes(params, callback, loading) {
    request('category/attributes', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}