/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id coupon.js 2022.3.4 $
 * @author mosir
 */

import { request } from '@/common/server.js'
import { ElMessage } from 'element-plus'

/**
 * 获取我的优惠券列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function myCouponList(params, callback, loading) {
    request('my/coupon/list', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取卖家管理的优惠券列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function sellerCouponList(params, callback, loading) {
    request('seller/coupon/list', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}


/**
 * 添加优惠券
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function couponAdd(params, callback, loading) {
    request('coupon/add', params, (res) => {
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
 * 更新优惠券信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function couponUpdate(params, callback, loading) {
    request('coupon/update', params, (res) => {
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
 * 删除优惠券信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function couponDelete(params, callback, loading) {
    request('coupon/delete', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    }, loading)
}