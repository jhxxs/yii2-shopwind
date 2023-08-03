/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id index.js 2021.10.16 $
 * @author mosir
 */

import { createRouter, createWebHistory } from 'vue-router'

/**
 * 登录后可访问控制：https://router.vuejs.org/zh/guide/advanced/meta.html
 */
const router = createRouter({
	//history: createWebHistory('/pc/'),
	history: createWebHistory(''), // 编译部署到线上服务器（public/pc目录下）需要
	routes: [{
		path: '/',
		component: () => import('@/pages/index/index.vue')
	}, {
		path: '/index/index',
		component: () => import('@/pages/index/index.vue')
	}, {
		path: '/my/index',
		component: () => import('@/pages/my/index/index.vue'),
		meta: {
			title: '我的',
			requiresAuth: true
		}
	}, {
		path: '/my/order/list',
		component: () => import('@/pages/my/order/list.vue'),
		meta: {
			title: '我的订单',
			requiresAuth: true
		}
	}, {
		path: '/my/order/detail/:id',
		component: () => import('@/pages/my/order/detail.vue'),
		meta: {
			title: '订单详情',
			requiresAuth: true
		}
	}, {
		path: '/my/order/logistic/:id',
		component: () => import('@/pages/my/order/logistic.vue'),
		meta: {
			requiresAuth: true
		}
	}, {
		path: '/my/order/evaluate/:id',
		component: () => import('@/pages/my/order/evaluate.vue'),
		meta: {
			title: '商品评价',
			requiresAuth: true
		}
	}, {
		path: '/my/refund/list',
		component: () => import('@/pages/my/refund/list.vue'),
		meta: {
			title: '我的退款',
			requiresAuth: true
		}
	}, {
		path: '/my/refund/build/:order_id',
		component: () => import('@/pages/my/refund/build.vue'),
		meta: {
			title: '申请退款',
			requiresAuth: true
		}
	}, {
		path: '/my/refund/detail/:id',
		component: () => import('@/pages/my/refund/detail.vue'),
		meta: {
			title: '退款详情',
			requiresAuth: true
		}
	}, {
		path: '/my/evaluate/list',
		component: () => import('@/pages/my/evaluate/list.vue'),
		meta: {
			title: '商品评价',
			requiresAuth: true
		}
	}, {
		path: '/my/integral/list',
		component: () => import('@/pages/my/integral/list.vue'),
		meta: {
			title: '我的积分',
			requiresAuth: true
		}
	}, {
		path: '/my/coupon/list',
		component: () => import('@/pages/my/coupon/list.vue'),
		meta: {
			title: '我的优惠券',
			requiresAuth: true
		}
	}, {
		path: '/my/cashcard/list',
		component: () => import('@/pages/my/cashcard/list.vue'),
		meta: {
			title: '我的充值卡',
			requiresAuth: true
		}
	}, {
		path: '/my/favorite/goods',
		component: () => import('@/pages/my/favorite/goods.vue'),
		meta: {
			title: '我收藏的商品',
			requiresAuth: true
		}
	}, {
		path: '/my/favorite/store',
		component: () => import('@/pages/my/favorite/store.vue'),
		meta: {
			title: '我收藏的店铺',
			requiresAuth: true
		}
	}, {
		path: '/my/bank/list',
		component: () => import('@/pages/my/bank/list.vue'),
		meta: {
			title: '我的银行卡',
			requiresAuth: true
		}
	}, {
		path: '/my/mailbox/list',
		component: () => import('@/pages/my/mailbox/list.vue'),
		meta: {
			title: '我的短消息',
			requiresAuth: true
		}
	}, {
		path: '/my/bank/build',
		component: () => import('@/pages/my/bank/build.vue'),
		meta: {
			title: '提现设置',
			requiresAuth: true
		}
	}, {
		path: '/my/setting/profile',
		component: () => import('@/pages/my/setting/profile.vue'),
		meta: {
			requiresAuth: true
		}
	}, {
		path: '/my/setting/phone',
		component: () => import('@/pages/my/setting/phone.vue'),
		meta: {
			title: '修改手机号',
			requiresAuth: true
		}
	}, {
		path: '/my/setting/password',
		component: () => import('@/pages/my/setting/password.vue'),
		meta: {
			title: '修改账户密码',
			requiresAuth: true
		}
	}, {
		path: '/my/address/list',
		component: () => import('@/pages/my/address/list.vue'),
		meta: {
			title: '我的收货地址',
			requiresAuth: true
		}
	}, {
		path: '/seller/index',
		component: () => import('@/pages/seller/index/index.vue'),
		meta: {
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/order/list',
		component: () => import('@/pages/seller/order/list.vue'),
		meta: {
			title: '订单管理',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/order/detail/:id',
		component: () => import('@/pages/seller/order/detail.vue'),
		meta: {
			title: '订单详情',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/order/logistic/:id',
		component: () => import('@/pages/seller/order/logistic.vue'),
		meta: {
			title: '查看物流',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/refund/list',
		component: () => import('@/pages/seller/refund/list.vue'),
		meta: {
			title: '退款管理',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/refund/detail/:id',
		component: () => import('@/pages/seller/refund/detail.vue'),
		meta: {
			title: '退款详情',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/evaluate/list',
		component: () => import('@/pages/seller/evaluate/list.vue'),
		meta: {
			title: '评价管理',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/goods/list',
		component: () => import('@/pages/seller/goods/list.vue'),
		meta: {
			title: '商品列表',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/goods/build/:id?',
		component: () => import('@/pages/seller/goods/build.vue'),
		meta: {
			title: '商品发布',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/goods/stockwarn',
		component: () => import('@/pages/seller/goods/stockwarn.vue'),
		meta: {
			title: '商品库存预警',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/goods/picker',
		component: () => import('@/pages/seller/goods/picker.vue'),
		meta: {
			title: '商品采集',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/coupon/list',
		component: () => import('@/pages/seller/coupon/list.vue'),
		meta: {
			title: '优惠券管理',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/fullfree/index',
		component: () => import('@/pages/seller/fullfree/index.vue'),
		meta: {
			title: '满包邮设置',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/fullprefer/index',
		component: () => import('@/pages/seller/fullprefer/index.vue'),
		meta: {
			title: '满优惠设置',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/exclusive/index',
		component: () => import('@/pages/seller/exclusive/index.vue'),
		meta: {
			title: '手机专享设置',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/limitbuy/list',
		component: () => import('@/pages/seller/limitbuy/list.vue'),
		meta: {
			title: '秒杀商品',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/limitbuy/build/:id?',
		component: () => import('@/pages/seller/limitbuy/build.vue'),
		meta: {
			title: '设置秒杀商品',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/teambuy/list',
		component: () => import('@/pages/seller/teambuy/list.vue'),
		meta: {
			title: '拼团商品',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/teambuy/build/:id?',
		component: () => import('@/pages/seller/teambuy/build.vue'),
		meta: {
			title: '设置拼团商品',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/mealbuy/list',
		component: () => import('@/pages/seller/mealbuy/list.vue'),
		meta: {
			title: '搭配购商品',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/mealbuy/build/:id?',
		component: () => import('@/pages/seller/mealbuy/build.vue'),
		meta: {
			title: '设置搭配购商品',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/wholesale/list',
		component: () => import('@/pages/seller/wholesale/list.vue'),
		meta: {
			title: '批发商品',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/wholesale/build/:goods_id?',
		component: () => import('@/pages/seller/wholesale/build.vue'),
		meta: {
			title: '设置批发商品',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/store/setting',
		component: () => import('@/pages/seller/store/setting.vue'),
		meta: {
			title: '店铺设置',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/store/category',
		component: () => import('@/pages/seller/store/category.vue'),
		meta: {
			title: '店铺分类',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/store/swiper',
		component: () => import('@/pages/seller/store/swiper.vue'),
		meta: {
			title: '店铺轮播',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/store/banner',
		component: () => import('@/pages/seller/store/banner.vue'),
		meta: {
			title: '店铺招牌',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/store/map',
		component: () => import('@/pages/seller/store/map.vue'),
		meta: {
			title: '店铺地图位置',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/store/codpay',
		component: () => import('@/pages/seller/store/codpay.vue'),
		meta: {
			title: '货到付款',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/delivery/list',
		component: () => import('@/pages/seller/delivery/list.vue'),
		meta: {
			title: '运费模板列表',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/delivery/build/:id?',
		component: () => import('@/pages/seller/delivery/build.vue'),
		meta: {
			title: '配送运费模板',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/delivery/timer',
		component: () => import('@/pages/seller/delivery/timer.vue'),
		meta: {
			title: '配送时效',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/sms/index',
		component: () => import('@/pages/seller/sms/index.vue'),
		meta: {
			title: '短信提醒设置',
			requiresAuth: true,
			requiresSeller: true
		}
	}, {
		path: '/seller/login',
		component: () => import('@/pages/seller/login/login.vue'),
		meta: {
			title: '商家登录'
		}
	}, {
		path: '/user/login',
		component: () => import('@/pages/user/login.vue'),
		meta: {
			title: '用户登录'
		}
	}, {
		path: '/deposit/index',
		component: () => import('@/pages/deposit/index/index.vue'),
		meta: {
			title: '钱包',
			requiresAuth: true
		}
	}, {
		path: '/deposit/trade/list',
		component: () => import('@/pages/deposit/trade/list.vue'),
		meta: {
			title: '交易记录',
			requiresAuth: true
		}
	}, {
		path: '/deposit/trade/record',
		component: () => import('@/pages/deposit/trade/record.vue'),
		meta: {
			title: '对账单',
			requiresAuth: true
		}
	}, {
		path: '/deposit/trade/detail/:id',
		component: () => import('@/pages/deposit/trade/detail.vue'),
		meta: {
			title: '交易详情',
			requiresAuth: true
		}
	}, {
		path: '/deposit/trade/recharge',
		component: () => import('@/pages/deposit/trade/recharge.vue'),
		meta: {
			title: '在线充值',
			requiresAuth: true
		}
	}, {
		path: '/deposit/trade/drawal',
		component: () => import('@/pages/deposit/trade/drawal.vue'),
		meta: {
			title: '提现申请',
			requiresAuth: true
		}
	}, {
		path: '/deposit/setting/pay',
		component: () => import('@/pages/deposit/setting/pay.vue'),
		meta: {
			title: '支付设置',
			requiresAuth: true
		}
	}, {
		path: '/store/apply',
		component: () => import('@/pages/store/apply.vue'),
		meta: {
			title: '开店',
			requiresAuth: true
		}
	}, {
		path: '/store/index/:id',
		component: () => import('@/pages/store/index.vue'),
		meta: {
			title: '店铺'
		}
	}, {
		path: '/store/list/:id/:sid?',
		component: () => import('@/pages/store/list.vue'),
		meta: {
			title: '店铺商品'
		}
	}, {
		path: '/goods/detail/:id',
		component: () => import('@/pages/goods/detail.vue'),
		meta: {
			title: '商品详情'
		}
	}, {
		path: '/cart/index',
		component: () => import('@/pages/cart/index.vue'),
		meta: {
			title: '购物车',
			requiresAuth: true
		}
	}, {
		path: '/trade/order/normal',
		component: () => import('@/pages/trade/order/normal.vue'),
		meta: {
			title: '确认订单',
			requiresAuth: true
		}
	}, {
		path: '/trade/cashier/pay/:bizIdentity/:bizOrderId',
		component: () => import('@/pages/trade/cashier/pay.vue'),
		meta: {
			title: '收银台',
			requiresAuth: true
		}
	}, {
		path: '/trade/cashier/result/:payTradeNo/:tradeNo?',
		component: () => import('@/pages/trade/cashier/result.vue'),
		meta: {
			title: '付款结果',
			requiresAuth: true
		}
	}, {
		path: '/webim/chat/:id?/:store_id?',
		component: () => import('@/pages/webim/chat.vue'),
		meta: {
			title: '与客服聊天',
			requiresAuth: true
		}
	}, {
		path: '/search/goods/:id?',
		component: () => import('@/pages/search/goods.vue'),
		meta: {
			title: '搜索商品'
		}
	}, {
		path: '/search/store/:id?',
		component: () => import('@/pages/search/store.vue'),
		meta: {
			title: '搜索店铺'
		}
	}, {
		path: '/category/goods',
		component: () => import('@/pages/category/goods.vue'),
		meta: {
			title: '所有商品分类'
		}
	}, {
		path: '/connect/callback',
		component: () => import('@/pages/connect/callback.vue'),
		meta: {
			title: '授权登录'
		}
	}, {
		path: '/message/result',
		component: () => import('@/pages/message/result.vue'),
		meta: {
			title: '提示'
		}
	}, {
		path: '/article/detail/:id?',
		component: () => import('@/pages/article/detail.vue'),
		meta: {
			title: '文章详情'
		}
	}, {
		path: '/channel/page/:id?',
		component: () => import('@/pages/channel/page.vue'),
		meta: {
			title: '自定义页'
		}
	}]
})

export default router
