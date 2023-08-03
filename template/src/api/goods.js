/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id goods.js 2022.4.2 $
 * @author mosir
 */

import { ElMessage } from 'element-plus'
import { request } from '@/common/server.js'
import { redirect } from '@/common/util.js'

/**
 * 获取商品列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function goodsList(params, callback, loading) {
    request('goods/list', Object.assign({ if_show: 1 }, params), (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 搜索商品（多条件）
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function goodsSearch(params, callback, loading) {
    request('goods/search', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 读取单个商品的信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 * @param {Boolean} force 
 */
export function goodsRead(params, callback, loading, force) {
    request('goods/read', params, (res) => {
        if (res.code == 0) {
            if (force) {
                if (!res.data || !res.data.if_show || res.data.closed) {
                    redirect('/message/result', 'warn', '商品不可售', '该商品已被下架或店铺已关闭')
                }
            }
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 读取单个商品的规格信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function goodsSpecs(params, callback, loading) {
    request('goods/specs', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 读取单个商品的主图（轮播图）信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function goodsImages(params, callback, loading) {
    request('goods/images', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 插入商品信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function goodsAdd(params, callback, loading) {
    request('goods/add', params, (res) => {
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
 * 更新商品信息
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function goodsUpdate(params, callback, loading) {
    request('goods/update', params, (res) => {
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
 * 删除商品
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function goodsDelete(params, callback, loading) {
    request('goods/delete', params, (res) => {
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
 * 获取商品属性
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function goodsAttributes(params, callback, loading) {
    request('goods/attributes', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取商品销量列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function goodsSalelogs(params, callback, loading) {
    request('goods/salelogs', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取商品评价列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function goodsComments(params, callback, loading) {
    request('goods/comments', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}

/**
 * 获取库存预警商品列表
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function goodsStockwarn(params, callback, loading) {
    request('goods/stockwarn', params, (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        }
    }, loading)
}



