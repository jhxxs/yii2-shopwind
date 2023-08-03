<template>
	<myhead :exclude="['category']"></myhead>
	<div class="main bgf">
		<div class="w" style="padding-top: 20px;">
			<el-steps :active="1" finish-status="success" process-status="process" simple>
				<el-step title="选择商品结算" />
				<el-step title="确认订单信息" />
				<el-step title="付款" />
				<el-step title="确认收货" />
				<el-step title="评价" />
			</el-steps>
			<div v-loading="loading" style="min-height: 100px;">
				<div v-if="Object.values(orders).length > 0">
					<div v-if="orders.gtype.includes('material')" class="addresses mt20">
						<div class="f-16 bold f-yahei mb10 flex-middle">
							<el-icon>
								<LocationInformation />
							</el-icon>
							<span class="ml5">收货地址</span>
						</div>
						<div class="uni-flex uni-row flex-wrap">
							<div @click="address(item)" v-for="(item) in addresses.list"
								:class="['item pointer pd10 mr10 mb10 relative', item.addr_id == form.addr_id ? 'selected' : '']">
								<p>{{ item.consignee }} {{ item.phone_mob || item.phone_tel }}</p>
								<p class="f-13 f-c55 mt5">
									{{ item.province }} {{ item.city || '' }} {{ item.district || '' }} {{ item.address
									}}
								</p>
								<el-icon v-if="item.addr_id == form.addr_id" class="ico absolute"><Select /></el-icon>
							</div>
							<router-link :to="'/my/address/list?redirect='+ getUrl()" class="item rlink pointer pd10 mr10 mb10 center">
								<el-icon :size="20">
									<Plus />
								</el-icon>
								<p>使用新地址</p>
							</router-link>
						</div>
					</div>
					<div class="cartbox mt20 f-13">
						<div class="hd mb20">
							<el-row>
								<el-col :span="12"><span class="ml20">店铺商品</span></el-col>
								<el-col :span="4" class="center">价格</el-col>
								<el-col :span="4" class="center">数量</el-col>
								<el-col :span="4" class="center">小计</el-col>
							</el-row>
						</div>
						<div class="bd">
							<div v-for="(order, store_id) in orders.orderList" class="list mb10">
								<el-row class="pt10 pb10">
									<el-col :span="24" class="uni-flex uni-row flex-middle">
										<label>店铺：</label>
										<span class="f-blue">{{ order.store_name }}</span>
									</el-col>
								</el-row>
								<div v-for="(goods) in order.items" class="item">
									<el-row class="flex-middle">
										<el-col :span="12" class="uni-flex uni-row flex-middle">
											<router-link :to="'/goods/detail/' + goods.goods_id"
												class="pic ml10 mr10 pt5 pb5">
												<img width="70" height="70" :src="goods.goods_image" />
											</router-link>
											<div class="desc mr10 pt5 pb5 f-14">
												<router-link :to="'/goods/detail/' + goods.goods_id" class="rlink">
													<el-tag v-if="goods.gtype =='virtual'" class="f-12">虚拟商品</el-tag>
													<el-tag v-else-if="goods.gtype =='service'" class="f-12">服务商品</el-tag>
													{{ goods.goods_name }}
												</router-link>
												<p v-if="goods.specification" class="f-gray f-12 mt5">
													{{ goods.specification }}
												</p>
											</div>
										</el-col>
										<el-col :span="4" class="center f-red">{{ currency(goods.price) }}</el-col>
										<el-col :span="4" class="center">{{ goods.quantity }}</el-col>
										<el-col :span="4" class="center f-red f-14">{{ currency(goods.subtotal) }}
										</el-col>
									</el-row>
								</div>
								<el-row class="extra">
									<el-col :span="14">
										<div class="uni-flex uni-row pd10 flex-middle mt10">
											<span>买家留言：</span>
											<el-input v-model="form.postscript[store_id]" class="postscript"
												placeholder="对商品的特殊需求，如颜色、尺码等" clearable />
										</div>
									</el-col>
									<el-col :span="10" class="cost pt10 pb10">
										<div v-if="form.otype != 'guidebuy' && shippings[store_id]"
											class="uni-flex uni-row flex-middle each">
											<p class="pt10 pb10">
												<label class="mr10 f-c55">配送方式：</label>
												<el-select v-model="form.delivery_type[store_id]"
													@change="delivery(store_id)">
													<el-option
														v-for="(shipping, type, index) in shippings[store_id][form.region_id]"
														:value="type"
														:label="shipping.name + ' : ' + currency(shipping.logistic_fees)" />
												</el-select>
											</p>
											<p class="center pd10 f-red f-14">+
												{{
														currency(shippings[store_id][form.region_id][form.delivery_type[store_id]].logistic_fees)
												}}
											</p>
										</div>
										<div v-if="order.allow_coupon == 1 && order.coupon_list.length > 0"
											class="uni-flex uni-row flex-middle each">
											<p class="pt10 pb10">
												<label class="mr10 f-c55">商家优惠：</label>
												<el-select v-model="form.coupon_sn[store_id]"
													@change="coupon(store_id)">
													<el-option value="0" label="不使用优惠券" />
													<el-option v-for="(item) in order.coupon_list"
														:value="item.coupon_sn"
														:label="item.coupon_name + ' : ' + currency(item.coupon_value)" />
												</el-select>
											</p>
											<p class="center f-red f-14">
												-{{ currency(couponValue(store_id)) }}
											</p>
										</div>
										<div v-if="order.fullprefer.price > 0"
											class="uni-flex uni-row flex-middle each">
											<p class="pt10 pb10">
												<label class="mr10 f-c55">满减优惠：</label>
												<span>购满{{ currency(order.fullprefer.amount) }}元，减{{
														currency(order.fullprefer.price)
												}}元</span>
											</p>
											<p class="center f-red f-14">
												-{{ currency(order.fullprefer.price) }}
											</p>
										</div>
										<div class="uni-flex uni-row flex-middle each">
											<p class="pt10 pb10">
												<label class="f-red">店铺合计：</label>
											</p>
											<p class="center f-red f-14">
												{{ currency(orders.orderList[store_id].paymoney) }}
											</p>
										</div>
									</el-col>
								</el-row>
							</div>
							<el-row v-if="orders.allow_integral == 1 && integralExchange.points > 0">
								<el-col :span="14"></el-col>
								<el-col :span="6">
									<p class="pd10 ml10">
										<label class="mr10">积分抵扣：</label>
										<span :class="[integralExchange.useIntegral ? '' : 'f-gray']">
											<el-switch v-model="integralExchange.useIntegral" @change="integral"
												class="mr10" />
											可使用{{ integralExchange.points }}积分抵扣
										</span>
									</p>
								</el-col>
								<el-col :span="4" class="center f-14">
									<p class="pd10 f-red">
										- {{ currency(integralExchange.useIntegral ? integralExchange.money : 0) }}
									</p>
								</el-col>
							</el-row>
						</div>
						<div class="fd">
							<el-row class="pt10 pb10 mt10">
								<el-col :span="5" class="uni-flex uni-row flex-middle">
									<router-link to="/cart/index" class="rlink ml20 vertical-middle f-green">
										<el-icon :size="12">
											<DArrowLeft />
										</el-icon>
										<span>返回购物车</span>
									</router-link>
								</el-col>
								<el-col :span="19" class="vertical-middle flex-end f-15">
									<label>总价：</label>
									<span class="mr20 f-17 f-red">{{ currency(orders.paymoney) }}</span>
									<el-button @click="submit" type="primary" size="large" class="mr10">去结算</el-button>
								</el-col>
							</el-row>
						</div>
					</div>
				</div>
			</div>
			<div v-if="!loading && Object.values(orders).length == 0"
				class="empty uni-flex uni-row flex-middle flex-center">
				<p class="pd10"><img src="@/assets/images/cart_empty.png" width="83" /></p>
				<div class="ml20">
					<p class="bold mb20">您未选择结算商品</p>
					<router-link to="/cart/index" class="rlink f-blue f-13 f-yahei">返回购物车<span
							class="f-simsun">>></span>
					</router-link>
				</div>
			</div>
		</div>
	</div>
	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessageBox } from 'element-plus'
import { currency, isEmpty, redirect, getUrl } from '@/common/util.js'
import { orderBuild, orderCreate } from '@/api/order.js'

import myhead from '@/pages/layout/header/home.vue'
import myfoot from '@/pages/layout/footer/home.vue'

const loading = ref(true)
const orders = reactive({gtype:[]})
const addresses = reactive({ list: [] })
const shippings = reactive({})
const integralExchange = reactive({ points: 0, money: 0, useIntegral: false })
const form = reactive({ otype: 'normal', addr_id: 0, region_id: 0, consignee: '', phone_mob: '', delivery_type: {}, postscript: {}, coupon_sn: {}, exchange_integral: 0, anonymous: {} })

onMounted(() => {
	orderBuild(form, (data) => {

		Object.assign(orders, data.list)
		Object.assign(shippings, data.shipping_methods || {})
		addresses.list = data.my_address

		// 绑定默认收货地址
		defaddress()

		// 绑定默认优惠券
		defcoupon()

		// 绑定积分抵扣
		defintegral()

		// 计算支付金额
		buildMoney()

		// 如果选择结算的商品类型既有实物商品也有虚拟商品，则不予结算
		if(orders.gtype.length > 1) {
			return ElMessageBox.alert('实物商品和虚拟商品因结算周期不同，不支持一起结算，请重新选择结算商品', '提示', {
    			confirmButtonText: 'OK',
				callback: function(action, instance) {
					redirect('cart/index')
				}
  			})
		}
	}, loading)
})

/**
 * 绑定默认收货地址
 * 要考虑第一次加载和切换收货地址后加载的情况
 */
function defaddress() {
	if (isEmpty(addresses.list)) {
		return false
	}

	let address = null
	if (form.addr_id) {
		address = addresses.list[form.addr_id]
	}

	// 如果没有选择收货地址，先取默认收货地址（目前接口返回的地址数据，第一条地址也即默认收货地址）
	if (isEmpty(address)) {
		address = Object.values(addresses.list)[0]
	}

	Object.assign(form, isEmpty(address) ? {} : {
		addr_id: address.addr_id,
		region_id: address.region_id,
		gtype: orders.gtype[0]
	})

	// 绑定默认配送方式
	defshippings(form.region_id)
}

/**
 * 切换收货地址
 */
function address(item) {
	form.addr_id = item.addr_id
	form.region_id = item.region_id
	buildMoney()
}

/**
 * 绑定积分抵扣
 */
function defintegral() {
	if (orders.allow_integral == 1) {
		let integral = orders.integralExchange
		let points = integral.maxPoints > integral.userIntegral ? integral.userIntegral : integral.maxPoints
		integralExchange.points = points
		integralExchange.money = integral.rate * points
		integralExchange.useIntegral = true
		form.exchange_integral = points
	}
}

/**
 * 绑定默认优惠券
 */
function defcoupon() {
	orders.storeIds.forEach((store_id) => {
		let list = orders.orderList[store_id].coupon_list
		if (!isEmpty(list)) {
			form.coupon_sn[store_id] = list[0].coupon_sn
		}
	})
}

/**
 * 切换优惠券
 * @param {Object} store_id
 */
function coupon(store_id) {
	// 有可能因为选择不使用优惠券后导致积分抵扣数有误，所以重置选择积分
	defintegral()
	buildMoney()
}

function couponValue(store_id) {
	let list = orders.orderList[store_id].coupon_list
	for (let index in list) {
		if (list[index].coupon_sn == form.coupon_sn[store_id]) {
			return parseFloat(list[index].coupon_value)
		}
	}
	return 0
}

/**
 * 绑定默认配送方式
 * @param {Object} deftoid
 */
function defshippings(deftoid) {
	for (let store_id in shippings) {
		form.delivery_type[store_id] = Object.values(shippings[store_id][deftoid])[0].type
	}
}

/**
 * 切换配送方式
 * @param {Object} store_id
 */
function delivery(store_id) {
	buildMoney()
}

/**
 * 切换积分抵扣
 */
function integral(value) {
	form.exchange_integral = value ? parseFloat(integralExchange.points) : 0
	buildMoney()
}

/**
 * 计算支付金额
 * 将所有涉及支付金额变化的放在这里计算并赋值
 */
function buildMoney() {

	let total = 0
	orders.storeIds.forEach((store_id) => {

		// 当前选择的快递方式
		let delivery_type = form.delivery_type[store_id] || 'express'

		// 当前收货地址的运费
		let logistic_fees = (isEmpty(shippings) || isEmpty(shippings[store_id]) || !form.region_id) ? 0 : parseFloat(
			shippings[store_id][form.region_id][delivery_type].logistic_fees)

		// 当前满减满折优惠
		let fullprefer = isEmpty(orders.orderList[store_id].fullprefer) ? 0 : parseFloat(orders
			.orderList[store_id].fullprefer.price)

		// 当前优惠券优惠
		let coupon_value = isEmpty(orders.orderList[store_id].coupon_list) ? 0 : couponValue(store_id)

		// 单个店铺实际需要支付的金额
		orders.orderList[store_id].paymoney = parseFloat(orders.orderList[store_id].amount) +
			logistic_fees -
			fullprefer -
			coupon_value

		// 所有店铺实际需要支付的金额
		total += orders.orderList[store_id].paymoney
	})

	// 积分抵扣
	if (form.exchange_integral > 0) {
		let tmptotal = total - integralExchange.money

		if (tmptotal < 0) {
			integralExchange.money = total
			form.exchange_integral = total / orders.integralExchange.rate
			tmptotal = 0
		}
		total = tmptotal
	}

	orders.paymoney = total
}

/**
 * 提交订单
 */
function submit() {
	orderCreate(form, (data) => {
		redirect('/trade/cashier/pay/'+data.bizIdentity.toLowerCase()+'/' + data.bizOrderId)
	})
}

</script>

<style scoped>
.addresses .item {
	border: 1px #BAD8FA solid;
	width: 208px;
	border-radius: 4px;
}

.addresses .item:first-child {
	margin-left: 0;
}

.addresses .item.selected {
	background-color: #fbf4ef;
	border-color: var(--el-color-danger);
}

.addresses .item.selected .absolute {
	bottom: 4px;
	right: 4px;
	color: var(--el-color-danger);
}

.cartbox {
	padding-bottom: 80px;
}

.cartbox .hd {
	border-top: 3px rgb(36, 107, 222) solid;
	height: 44px;
	line-height: 44px;
	box-shadow: 0 2px 12px 0px #f1f1f1;
}

.cartbox .bd .item {
	border: 1px #BAD8FA solid;
	border-top-width: 0;
}

.cartbox .bd .item:nth-child(2) {
	border-top-width: 1px;
}

.cartbox .extra {
	background-color: #F2F7FF;
}

.cartbox .cost .each p:first-child {
	width: 60%;
}

.cartbox .cost .each p:last-child {
	width: 40%;
}

.cartbox .postscript {
	width: 60%;
}

.cartbox .cost label {
	display: inline-block;
	max-width: 100px;
	text-align: right;
}

.cartbox .fd {
	background-color: #FCFCFF;
	margin-bottom: 50px;
}

.empty {
	padding: 0 0 100px;
}

:deep() .el-input__inner,
:deep() .el-input__inner::placeholder {
	font-size: 13px;
}
</style>