/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id selector.js 2019.10.19 $
 * @author winder
 */

import { promise } from '@/common/server.js'

/**
 * 多级联动组件（理论上兼容任意模型）
 * 返回选中项的ID及名称
 * @param {Object} multiIndex 
 * @param {Object} original 
 * @param {Object} api 
 * @param {Object} idField 
 * @param {Object} nameField 
 * @param {Object} parentField 
 * @param {Object} data 
 * @param {Object} index  
 */
export async function build(multiIndex, original = null, api = 'region/list', idField = 'region_id', nameField =
	'name', parentField = 'parent_id') {

	let data = await getData(multiIndex, original, api, idField, nameField, parentField)

	// 返回数据
	return getResult(data, multiIndex, idField, nameField)
}

/**
 * 循环获取数据
 * @param {Object} multiIndex 
 * @param {Object} original 
 * @param {Object} api 
 * @param {Object} idField 
 * @param {Object} nameField 
 * @param {Object} parentField 
 * @param {Object} data 
 * @param {Object} index 
 */
async function getData(multiIndex, original, api, idField, nameField, parentField, data = [], index = 0) {

	if (index == 0 || multiIndex[index - 1] > -1) {
		data[index] = await promise(api, {
			[parentField]: index > 0 ? data[index - 1].list[multiIndex[index - 1]][idField] : 0,
			if_show: 1,
			page_size: 1000
		})

	} else data[index] = []

	if (data[index].list && data[index].list.length > 0) {
		if (original != null && original.length > 0) {
			buildMultiIndex(data[index].list, multiIndex, original[index], nameField, index)
		}

		return getData(multiIndex, original, api, idField, nameField, parentField, data, ++index)
	}

	return data.length > 0 ? data.splice(0, data.length - 1) : data
}

/**
 * 使下拉选项保持选中的值
 * @param {Object} multiList
 * @param {Object} multiIndex
 * @param {Object} name
 * @param {Object} nameField
 * @param {Object} index
 */
function buildMultiIndex(multiList, multiIndex, name, nameField, index) {
	for (let key in multiList) {
		if (multiList[key][nameField] == name) {
			multiIndex[index] = parseInt(key)
		}
	}
}

/**
 * 返回选中的数据
 * @param {Object} data
 * @param {Object} multiIndex
 * @param {Object} idField
 * @param {Object} nameField
 */
function getResult(data, multiIndex, idField, nameField) {

	let array = []
	let multiList = []
	data.forEach((item, index) => {
		if (multiIndex[index] > -1) {
			array[index] = item.list[multiIndex[index]][nameField]
		}
		multiList[index] = item.list
	})

	return {
		id: (array.length > 0 && multiIndex[array.length - 1] > -1) ? multiList[array.length - 1][multiIndex[array.length - 1]][idField] : 0, // 末级ID
		label: array, // 当前选择的文本
		multiList: multiList
	}
}