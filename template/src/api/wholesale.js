/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id wholesale.js 2021.11.25 $
 * @author mosir
 */

import { request } from '@/common/server.js'
import { ElMessage } from 'element-plus'

/**
 * 获取卖家管理的拼团商品列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function sellerWholesaleList(params, callback, loading) {
    request('seller/wholesale/list', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取单条批发商品信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function wholesaleRead(params, callback, loading) {
    request('wholesale/read', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 新增/编辑批发商品信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function wholesaleUpdate(params, callback, loading) {
    request('wholesale/update', params, (res) => {
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
 * 删除批发商品信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function wholesaleDelete(params, callback, loading) {
    request('wholesale/delete', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    }, loading)
}