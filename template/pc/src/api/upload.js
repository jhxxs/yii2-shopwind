/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id upload.js 2021.10.25 $
 * @author mosir
 */

import { upload } from '@/common/server.js'
import { ElMessage } from 'element-plus'

/**
 * 上传文件
 * @param {File} file
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function uploadFile(file, params, callback, loading) {
    upload('upload/file', file, Object.assign({ fileval: 'file' }, params), (res) => {
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
 * 上传文件并保存到数据库
 * @param {File} file
 * @param {Object} params 
 * @param {Function} callback 
 * @param {ElLoading} loading 
 */
export function uploadAdd(file, params, callback, loading) {
    upload('upload/add', file, Object.assign({ fileval: 'file' }, params), (res) => {
        if (res.code == 0) {
            if (typeof callback == 'function') {
                callback(res.data)
            }
        } else {
            ElMessage.warning(res.message)
        }
    }, loading)
}