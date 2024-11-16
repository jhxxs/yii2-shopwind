/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id picker.js 2021.10.30 $
 * @author mosir
 */

import { request, promise } from '@/common/server.js'

/**
 * 采集数据并导入本地
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function pickerAdd(params, callback, loading) {
    request('picker/add', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res)
            }
        }
    }, loading)
}

/**
 * 获取采集平台商品列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function pickerGoods(params, callback, loading) {
    request('picker/goods', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取采集平台商品分类列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function pickerCategory(params, callback, loading) {
    request('picker/category', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取采集平台列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function pickerList(params, callback, loading) {
    request('picker/list', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}