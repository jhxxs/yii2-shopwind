<template>
	<myhead :exclude="['category', 'imagead']"></myhead>
	<div class="main bgf">
		<div class="w" style="padding-top: 20px;">
			<el-steps :active="0" finish-status="success" process-status="process" simple>
				<el-step title="选择商品结算" />
				<el-step title="确认订单信息" />
				<el-step title="付款" />
				<el-step title="确认收货" />
				<el-step title="评价" />
			</el-steps>
			<div v-loading="loading" style="min-height: 100px;">
				<div v-if="Object.values(cart.list).length > 0" class="cartbox mt20 f-13">
					<div class="hd mb20">
						<el-row class="center">
							<el-col :span="1">选择</el-col>
							<el-col :span="10">店铺商品</el-col>
							<el-col :span="3">价格</el-col>
							<el-col :span="3">数量</el-col>
							<el-col :span="3">小计</el-col>
							<el-col :span="4">操作</el-col>
						</el-row>
					</div>
					<div class="bd">
						<div v-for="(store, store_id) in cart.list" class="list mb10">
							<el-row v-if="Object.values(store.items).length > 0" class="pt10 pb10">
								<el-col :span="1" class="center flex-middle">
									<el-checkbox v-model="store.checkall" @change="(value) => { checkall(store_id) }" />
								</el-col>
								<el-col :span="23" class="uni-flex uni-row flex-middle">
									<label>店铺：</label>
									<router-link :to="'/store/index/' + store.store_id" class="rlink f-blue">{{
										store.store_name }}</router-link>
									<p v-if="store.storeFullPreferInfo" class="ml20">
										<el-tag type="warning">{{ store.storeFullPreferInfo.prefer.label }}</el-tag>
										<span class="ml10">{{ store.storeFullPreferInfo.text }}</span>
									</p>
								</el-col>
							</el-row>
							<div v-for="(goods, productid) in store.items" class="item">
								<el-row class="flex-middle">
									<el-col :span="1" class="center">
										<el-checkbox v-model="goods.selected" :true-label="1" :false-label="0"
											@change="(value) => { choseitem(store_id, [productid], goods.selected) }" />
									</el-col>
									<el-col :span="10" class="uni-flex uni-row flex-middle">
										<router-link :to="'/goods/detail/' + goods.goods_id" class="pic mr10 pt5 pb5">
											<img width="70" height="70" :src="goods.goods_image" />
										</router-link>
										<div class="desc mr10 pt5 pb5 f-14">
											<router-link :to="'/goods/detail/' + goods.goods_id" class="rlink">
												<el-tag v-if="goods.gtype == 'virtual'" class="f-12">虚拟商品</el-tag>
												<el-tag v-else-if="goods.gtype == 'service'" class="f-12">服务商品</el-tag>
												{{ goods.goods_name }}
											</router-link>
											<p v-if="goods.specification" class="f-gray f-12 mt5">
												{{ goods.specification }}
											</p>
										</div>
									</el-col>
									<el-col :span="3" class="center f-red">{{ currency(goods.price) }}</el-col>
									<el-col :span="3" class="center">
										<el-input-number @change="change(goods)" v-model="goods.quantity" size="small"
											:min="1" :max="goods.stock" />
									</el-col>
									<el-col :span="3" class="center f-red f-14">{{ currency(goods.subtotal) }}</el-col>
									<el-col :span="4" class="center f-blue">
										<p @click="collect(goods.goods_id)" class="mb5 pointer">加入收藏夹</p>
										<p @click="remove(goods)" class="pointer">移除</p>
									</el-col>
								</el-row>
							</div>
						</div>
					</div>
					<div class="fd">
						<el-row class="pt10 pb10 mt10">
							<el-col :span="1" class="center flex-middle">
								<el-checkbox v-model="cart.checkall" @change="(value) => { checkall() }" />
							</el-col>
							<el-col :span="2" class="uni-flex uni-row flex-middle">
								<label>全选</label>
							</el-col>
							<el-col :span="21" class="vertical-middle flex-end f-15">
								<label>总计：</label>
								<span class="mr20 f-17 f-red">{{ currency(cart.amount) }}</span>
								<el-button @click="submit" type="primary" size="large" class="mr10">
									确认并提交订单</el-button>
							</el-col>
						</el-row>
					</div>
				</div>
			</div>
			<div v-if="!loading && Object.values(cart.list).length == 0" class="empty uni-flex uni-row flex-middle">
				<p class="pd10"><img src="@/assets/images/cart_empty.png" width="83" /></p>
				<div class="ml20">
					<p class="bold mb20">您的购物车是空的，您可以</p>
					<router-link to="/" class="rlink f-blue f-13 f-yahei">选购商品<span class="f-simsun">>></span>
					</router-link>
				</div>
			</div>
		</div>
		<div class="w">
			<div v-if="gallery.length > 0" class="pt10 pb10 gallery">
				<div class="hd flex-middle center">
					<h3 class="f-20 f-yahei pl20 pr20 relative">猜你喜欢</h3>
				</div>
				<el-row :gutter="20" class="uni-flex uni-row bd mb20">
					<el-col :span="4" v-for="(item) in gallery" class="item">
						<router-link :to="'/goods/detail/' + item.goods_id" class="pic block rlink">
							<img :src="item.default_image" />
						</router-link>
						<router-link :to="'/goods/detail/' + item.goods_id" class="desc mt10 line-clamp-2 f-13 rlink">
							{{ item.goods_name }}
						</router-link>
						<div class="price pd10 mb10 mt5 center">
							<p v-if="item.promotion">
								<label class="del mr20 f-gray">{{ currency(item.price) }}</label>
								<span class="f-red">{{ currency(item.promotion.price) }}</span>
							</p>
							<p v-else class="f-red">{{ currency(item.price) }}</p>
						</div>
					</el-col>
				</el-row>
			</div>
		</div>
	</div>
	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage,ElMessageBox } from 'element-plus'

import { currency, isEmpty, redirect, unique } from '@/common/util.js'
import { cartList, cartChose, cartUpdate, cartRemove } from '@/api/cart.js'
import { goodsList } from '@/api/goods.js'
import { collectGoods } from '@/api/favorite.js'

import myhead from '@/pages/layout/header/home.vue'
import myfoot from '@/pages/layout/footer/home.vue'

const loading = ref(true)
const cart = reactive({ amount: 0, checkall: false, list: [], settle: 0 })
const gallery = ref([])
const form = reactive({ gtype: 'material' })

onMounted(() => {
	cartList(form, (data) => {
		cart.list = data.list || {}
		buildMoney()
	}, loading)

	goodsList({ page_size: 6 }, (data) => {
		gallery.value = data.list
	})
})

/**
 * 修改数量
 * @param {Object} item
 */
const change = (item) => {
	cartUpdate({ spec_id: item.spec_id, quantity: item.quantity }, (data) => {
		if (data) {
			ElMessage.success('数量已修改')
			reBuildByQuantity(data, item.store_id)
			buildMoney()
		}
	})
}

/**
 * 移除某个商品
 * @param {Object} item
 */
const remove = (item) => {
	cartRemove({ specs: [item.spec_id] }, (data) => {
		delete (cart.list[item.store_id].items[item.product_id])
		if (Object.values(cart.list[item.store_id].items).length == 0) {
			delete (cart.list[item.store_id])
		}

		// 因购买量变化执行阶梯价格
		reBuildByQuantity(data, item.store_id)
		buildMoney()
	})
}

/**
 * 确认并提交订单
 */
const submit = () => {
	if (cart.settle > 0) {
		let gtypes = []
		Object.values(cart.list).forEach((list) => {
			Object.values(list.items).forEach((item) => {
				if (item.selected == 1) gtypes.push(item.gtype)
			})
		})
		// 如果选择结算的商品类型既有实物商品也有虚拟商品，则不予结算
		if(unique(gtypes).length > 1) {
			return ElMessageBox.alert('实物商品和虚拟服务类商品因结算周期不同，不支持一起结算，请重新选择结算商品', '提示', {
    			confirmButtonText: 'OK'
  			})
		}
		return redirect('/trade/order/normal')
	}
	ElMessage.warning('请选择商品结算')
}
const collect = (value, target) => {
	collectGoods({ goods_id: value }, (data) => {
		ElMessage.success('收藏成功')
	})
}

/**
 * 只计算选中的商品金额
 */
function buildMoney() {

	// 先设置为选中，后面再调整
	cart.checkall = true

	// 购物车中选中结算的商品数量
	cart.settle = 0
	cart.amount = 0

	let list = cart.list
	for (let store_id in list) {
		list[store_id].checkall = true // 先设置店铺为选中，后面再调整
		for (let productid in list[store_id].items) {
			if (list[store_id].items[productid].selected == 1) {
				cart.settle++
				cart.amount += parseFloat(list[store_id].items[productid].subtotal)
			} else {
				// 如果有没选中的，重置全选为未选中
				cart.checkall = false
				list[store_id].checkall = false
			}
		}
	}
}

/**
 * 设置购物车商品为选中/取消
 * @param {Object} store_id
 * @param {Object} product_ids
 * @param {Object} selected
 */
function choseitem(store_id, product_ids, selected) {
	let items = cart.list[store_id].items

	cartChose({ product_ids: product_ids, selected: selected }, (data) => {
		for (let index in product_ids) {
			items[product_ids[index]].selected = selected
		}
		// 因购买量变化执行阶梯价格
		reBuildByQuantity(data, store_id)
		buildMoney()
	})
}

/**
 * 阶梯价格，重新赋值由于不同的购买量导致的结算价格变化
 * @param {Object} list
 * @param {Object} store_id
 */
function reBuildByQuantity(list, store_id) {
	if (isEmpty(list) || isEmpty(list.items)) {
		return false
	}

	for (let index in list.items) {
		if (store_id && list.items[index].store_id != store_id) {
			continue; // 如果传店铺，则不考虑其他店铺，不传说明是全选操作
		}
		Object.assign(cart.list[list.items[index].store_id].items[index], list.items[index])
	}
}

/**
 * 全选/取消购物车商品
 * @param {Object} id
 */
function checkall(id) {
	let product_ids = []
	let list = cart.list
	let selected = (id ? cart.list[id].checkall : cart.checkall) ? 1 : 0

	// 先找出所有购物车商品productid
	for (let store_id in list) {

		// 只改变指定店铺
		if (id && store_id != id) { continue }

		for (let product_id in list[store_id].items) {
			product_ids.push(product_id)
		}
	}

	cartChose({ product_ids: product_ids, selected: selected }, (data) => {
		if (data) {
			for (let store_id in list) {

				// 只改变指定店铺
				if (id && store_id != id) { continue }

				for (let productid in list[store_id].items) {
					list[store_id].items[productid].selected = Number(selected)
				}
			}
			// 因购买量变化执行阶梯价格
			reBuildByQuantity(data)
			buildMoney()
		}
	})
}

</script>

<style scoped>
.cartbox .hd {
	border-top: 3px rgb(36, 107, 222) solid;
	height: 44px;
	line-height: 44px;
	box-shadow: 0 2px 12px 0px #f1f1f1;
}

.cartbox .bd .item {
	border: 1px #BAD8FA solid;
	border-bottom: 0;
}

.cartbox .bd .item:last-child {
	border-bottom: 1px #BAD8FA solid;
}

.cartbox .fd {
	/* box-shadow: 0 1px 4px -1px #f1f1f1; */
	background-color: #FCFCFF;
}

.empty {
	padding: 0 0 50px 100px;
}

.gallery .hd {
	padding: 80px 0 50px;
}

.gallery .hd h3 {
	width: 150px;
	padding: 0 25px;
}

.gallery .hd h3::before,
.gallery .hd h3::after {
	content: "";
	position: absolute;
	top: 50%;
	margin-top: -10px;
	background: url('@/assets/images/sprite.png') no-repeat;
	width: 25px;
	height: 20px;
	left: 0;
}

.gallery .hd h3::after {
	left: auto;
	right: 0;
}

.gallery .item .pic {
	border: 1px #f5f5f5 solid;
	border-radius: 4px;
}

.gallery .item .pic:hover {
	box-shadow: 0 3px 20px rgba(0, 0, 0, .06);
}

.gallery .item img {
	width: 100%;
	height: 100%;
	border-radius: 4px;
}
</style>
