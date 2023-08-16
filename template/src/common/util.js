/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id util.js 2022.2.6 $
 * @author mosir
 */

import router from '@/router'

/**
 * 获取页面域名
 */
export function siteUrl() {
	return window.location.protocol + "//" + window.location.host
}

/**
 * 获取页面完整路径
 */
export function getUrl(scheme) {
	return scheme ? location.href : location.href.replace(siteUrl(), '');
}

/**
 * 金额格式化
 * @param  {Float} value 传进来的值
 * @param  {Int} decimals 小数位数
 * @param  {String} symbol 货币符号
 */
export function currency(value, decimals, symbol) {

	value = value ? parseFloat(value) : 0
	if (!isFinite(value) || (!value && value !== 0)) return ''
	symbol = symbol != null ? symbol : '¥' // 默认货币符号
	decimals = decimals != null ? decimals : 2

	var stringified = Math.abs(value).toFixed(decimals)
	var _int = decimals ?
		stringified.slice(0, -1 - decimals) :
		stringified
	var i = _int.length % 3
	var head = i > 0 ?
		(_int.slice(0, i) + (_int.length > 3 ? ',' : '')) :
		''
	var radix = decimals ?
		stringified.slice(-1 - decimals) :
		''
	var sign = value < 0 ? '-' : ''

	const digitsRE = /(\d{3})(?=\d)/g
	var digits = _int.slice(i).replace(digitsRE, '$1,')

	return sign + symbol + (symbol == '' ? head.replace(',', '') : head) + digits + radix
}

/**
 * 格式化订单/交易状态
 * @param {Object} value
 * @param {String} type
 */
export function translator(value, type = 'order') {
	let array = {}
	if (type == 'trade') {
		array = {
			'PENDING': '待付款',
			'ACCEPTED': '待发货',
			'SHIPPED': '待收货',
			'USING': '待使用',
			'CLOSED': '交易关闭',
			'SUCCESS': '交易完成',
			'VERIFY': '待审核'
		}
	} else if (type == 'refund') {
		array = {
			'CLOSED': '退款关闭',
			'SUCCESS': '退款成功',
			'SELLER_REFUSE_BUYER': '商家拒绝退款，等待买家修改中',
			'WAIT_SELLER_AGREE': '买家申请退款，等待商家同意',
			'WAIT_SELLER_CONFIRM': '退款申请等待商家确认中'
		}
	} else {
		array = {
			11: '待付款',
			19: '待成团',
			20: '待发货',
			30: '待收货',
			35: '待配送',
			36: '待取货',
			38: '待使用',
			40: '交易完成',
			0: '交易关闭',
		}
	}
	if (array[value]) {
		return array[value]
	}
	return value
}

/**
 * 判断对象/数组是否为空
 * @param {Object} object
 */
export function isEmpty(object) {
	if (typeof object == 'undefined' || object == 'undefined' || object == undefined || object == null || object == '') {
		return true
	}
	return false
}

/**
 * 截取字符串
 * @param {String} string 
 * @param {Number} length 
 * @param {String} etc 
 */
export function truncate(string, length = 80, etc = '...') {
	if (isEmpty(string)) {
		return string
	}
	if (string.length <= length) {
		return string
	}
	return string.substring(0, length) + etc
}

/**
 * 倒计时
* @param {Number} seconds
* @param {Function} callback
 */
export function countDown(seconds, callback) {
	let timer = setInterval(() => {
		seconds--
		if (typeof callback == 'function') {
			callback(seconds)
		}
		if (seconds <= 0) {
			clearInterval(timer)
		}
	}, 1000)
}

/**
 * 跳转到页面
 * @param {String} url 
 * @param {String} status  warn/success/fail/clock/info
 * @param {String} title 
 * @param {String} content 
 */
export function redirect(url, status = '', title = '', content = '') {
	if (!url) return false

	if (status || title || content) {
		localStorage.setItem('redirectMessage', JSON.stringify({ icon: status, title: title, content: content }))
	}

	url = url.replace(siteUrl(), '')
	if (url.substring(0, 1) != '/') {
		url = '/' + url
	}
	router.replace(url)
}

/**
 * 数组去重
 * @param {Object} arr
 */
export function unique(arr) {
	//Set数据结构，它类似于数组，其成员的值都是唯一的
	return Array.from(new Set(arr)); // 利用Array.from将Set结构转换成数组
}

/**
 * 判断某个元素是否在数组里面
 * @param {Object} search
 * @param {Object} array
 */
export function inArray(search, array) {
	for (let index in array) {
		if (array[index] == search) {
			return true
		}
	}
	return false
}

/**
 * 判断是否为移动端
 */
export function isMobile() {
	if (/Mobi|Android|iPhone/i.test(navigator.userAgent)) {
		return true
	}
	if (document.body.clientWidth <= 750) {
		return true
	}
	return false
}

export function isIE() {
	if (navigator.userAgent.indexOf("MSIE") > 0) {
		return true
	}
	return false
}

