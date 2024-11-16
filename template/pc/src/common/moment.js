/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id moment.js 2021.11.9 $
 * @author winder
 */

import moment from 'moment'

const range = {}

/**
 * 获取现在的时间
 */
export function getMoment() {
	return moment()
}

/**
 * 获取今日的开始结束时间
 */
export function getToday() {
	range.starttime = moment(moment().startOf("day").valueOf()).format("YYYY-MM-DD HH:mm:ss");
	range.endtime = moment(moment().valueOf()).format("YYYY-MM-DD HH:mm:ss");
	return range
}

/**
 * 获取昨日的开始结束时间
 */
export function getYesterday() {
	range.starttime = moment(moment().add(-1, 'days').startOf("day").valueOf()).format("YYYY-MM-DD HH:mm:ss");
	range.endtime = moment(moment().add(-1, 'days').endOf('day').valueOf()).format('YYYY-MM-DD HH:mm:ss');
	return range
}

/**
 * 获取本周的开始结束时间
 */
export function getCurrWeekDays() {
	range.starttime = moment(moment().week(moment().week()).startOf('week').add(1, 'days').valueOf()).format(
		'YYYY-MM-DD HH:mm:ss')
	range.endtime = moment(moment().week(moment().week()).endOf('week').add(1, 'days').valueOf()).format(
		'YYYY-MM-DD HH:mm:ss');
	return range
}

/**
 * 获取上一周的开始结束时间
 */
export function getLastWeekDays() {
	range.starttime = moment(moment().week(moment().week() - 1).startOf('week').add(1, 'days').valueOf()).format(
		'YYYY-MM-DD HH:mm:ss')
	range.endtime = moment(moment().week(moment().week() - 1).endOf('week').add(1, 'days').valueOf()).format(
		'YYYY-MM-DD HH:mm:ss');
	return range
}

/**
 * 获取最近7天的开始结束时间
 */
export function getLast7Days() {
	range.starttime = moment().subtract(7, 'days').add(1, 'days').format('YYYY-MM-DD 00:00:00')
	range.endtime = moment().format('YYYY-MM-DD HH:mm:ss')
	return range
}

/**
 * 获取当月的开始结束时间
 */
export function getCurrMonthDays() {
	range.starttime = moment().startOf('month').format('YYYY-MM-DD HH:mm:ss')
	range.endtime = moment().endOf('month').format('YYYY-MM-DD HH:mm:ss')
	return range
}

/**
 * 获取上一个月的开始结束时间
 */
export function getLastMonthDays() {
	range.starttime = moment(moment().month(moment().month() - 1).startOf('month').valueOf()).format(
		'YYYY-MM-DD HH:mm:ss');
	range.endtime = moment(moment().month(moment().month() - 1).endOf('month').valueOf()).format(
		'YYYY-MM-DD HH:mm:ss');
	return range
}

/**
 * 获取最近30天的开始结束时间
 */
export function getLast30Days() {
	range.starttime = moment().subtract(30, 'days').add(1, 'days').format('YYYY-MM-DD 00:00:00')
	range.endtime = moment().format('YYYY-MM-DD HH:mm:ss')
	return range
}

/**
 * 获取本年的开始结束时间
 */
export function getCurrYearDays() {
	range.starttime = moment().startOf('year').format('YYYY-MM-DD HH:mm:ss')
	range.endtime = moment().endOf('year').format('YYYY-MM-DD HH:mm:ss')
	return range
}
