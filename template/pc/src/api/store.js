/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id store.js 2022.3.25 $
 * @author mosir
 */

import { ElMessageBox, ElMessage } from 'element-plus'
import { request } from '@/common/server.js'
import { redirect } from '@/common/util.js'

/**
 * 获取店铺基本信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 * @param {Boolean} force 
 */
export function storeRead(params, callback, loading, force) {
    request('store/read', params, (res) => {
        if (res.code == 0) {
            if (force) {
                if (res.data.state == 2) {
                    redirect('/message/result', 'fail', '店铺关闭', '该店铺已被关闭，请稍后再访问')
                }
            }
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取店铺列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function storeList(params, callback, loading) {
    request('store/list', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取店铺轮播图
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function storeSwiper(params, callback, loading) {
    request('store/swiper', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取店铺证件信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function storePrivacy(params, callback, loading) {
    request('store/privacy', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 提交开店信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function storeAdd(params, callback, loading) {
    request('store/add', params, (res) => {
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
 * 更新店铺基本信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function storeUpdate(params, callback, loading) {
    request('store/update', params, (res) => {
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
 * 获取店铺等级列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function storeGradeList(params, callback, loading) {
    request('store/grades', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取店铺动态评分
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function storeDynamiceval(params, callback, loading) {
    request('store/dynamiceval', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取店铺满优惠信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function storeFullprefer(params, callback, loading) {
    request('store/fullprefer', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 设置店铺货到付款
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function storeCodpay(params, callback, loading) {
    request('store/codpay', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

