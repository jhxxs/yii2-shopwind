<template>
	<myhead :exclude="['category', 'imagead']"></myhead>
	<div class="main bgf">
		<div class="banner" :style="{ 'background-image': store.pcbanner ? 'url(' + store.pcbanner + ')' : '' }"></div>
		<div class="menus">
			<ul class="uni-flex uni-row w f-white">
				<li @click="redirect('/store/index/' + goods.store_id)" class="pointer pl20 pr20"><span
						class="pl10 pr10">店铺首页</span></li>
				<li @click="redirect('/store/list/' + goods.store_id)" class="pointer pl20 pr20"><span
						class="pl10 pr10">所有商品</span></li>
				<li @click="redirect('/store/list/' + goods.store_id + '?orderby=sales|desc')" class="pointer pl20 pr20">
					<span class="pl10 pr10">镇店之宝</span>
				</li>
				<li @click="redirect('/store/list/' + goods.store_id + '?orderby=add_time|desc')" class="pointer pl20 pr20">
					<span class="pl10 pr10">新款上市</span>
				</li>
				<li @click="redirect('/store/list/' + goods.store_id + '?orderby=views|desc')" class="pointer pl20 pr20">
					<span class="pl10 pr10">人气热款</span>
				</li>
				<li @click="redirect('/store/list/' + goods.store_id + '?orderby=comments|desc')" class="pointer pl20 pr20">
					<span class="pl10 pr10">好评返购</span>
				</li>
				<li class="selected pl20 pr20"><span class="pl10 pr10">商品详情</span> </li>
			</ul>
		</div>
		<div class="w">
			<el-row class="mb20 mt20">
				<el-col class="uni-flex uni-row location">
					<p class="f-gray">所有分类<span class="f-simsun ml10 mr10">></span></p>
					<p v-for="(item) in goods.category">
						<router-link class="rlink mb5" :to="'/search/goods/' + item.cate_id">
							{{ item.cate_name }}
						</router-link>
						<span class="f-simsun ml10 mr10">></span>
					</p>
					<p class="f-gray">商品详情</p>
				</el-col>
			</el-row>
			<el-row class="mb20" v-loading="loading">
				<el-col :span="8">
					<div class="carousel">
						<p class="preview"><img class="block" :src="goods.default_image" /></p>
						<swiper :navigation="true" :spaceBetween="20" :slidesPerView="4" :modules="modules"
							@click="slideClick" class="mt10">
							<swiper-slide :class="[myswiper.selected == index ? 'selected' : '']"
								v-for="(item, index) in goods.images"><img :src="item" /></swiper-slide>
						</swiper>
						<div class="uni-flex uni-row pd10 f-12 f-gray mt20 flex-center">
							<p @click="preview = true" class="vertical-middle mr20 pointer">
								<el-icon :size="14">
									<Search />
								</el-icon><span class="ml5">查看大图</span>
							</p>
							<p @click="collect('goods')"
								:class="['vertical-middle ml20 mr20 pointer', goods.becollected ? 'f-red' : '']">
								<el-icon :size="14">
									<Star />
								</el-icon><span class="ml5">加入收藏</span>
							</p>
							<p @click="redirect('/webim/chat/' + goods.store_id + '/' + goods.store_id)"
								class="vertical-middle ml20 pointer">
								<el-icon :size="14">
									<Service />
								</el-icon><span class="ml5">联系客服</span>
							</p>
							<el-image-viewer v-if="preview" :onClose="closePreview" :url-list="goods.images" />
						</div>
					</div>
				</el-col>
				<el-col :span="16">
					<div class="ml20">
						<div class="title mb20">
							<p class="f-16 ">{{ goods.goods_name }}</p>
							<p v-if="goods.tags" class="f-gray mt5">{{ goods.tags }}</p>
						</div>
						<div v-if="goods.promotion"
							class="promotes pl10 pr10 pt5 pb5 f-white uni-flex uni-row vertical-middle f-13">
							<p class="ml10 pl5 mr20 pt5 pb5">{{ goods.promotion.name || '限时抢购' }}</p>
							<div v-if="goods.promotion.timestamp > 0" class="clock vertical-middle">
								<el-icon>
									<Clock />
								</el-icon>
								<span class="ml5">还剩</span>
								<countdown :second="goods.promotion.timestamp" :showColon="false" splitorColor="#ffffff">
								</countdown>
							</div>
						</div>
						<div class="boxes pd10 f-yahei">
							<dl class="uni-flex uni-row f-13 f-c55 ml10 mb10 mt5">
								<dt>市<span style="margin:0 7px;">场</span>价：</dt>
								<dd class="f-gray f-16">
									<del v-if="goods.mkprice > 0">{{ currency(goods.mkprice) }}</del>
									<del v-else>{{ currency(goods.price * 1.5) }}</del>
								</dd>
							</dl>
							<dl class="uni-flex uni-row f-13 f-c55 ml10 mb10">
								<dt>商<span style="margin:0 7px;">城</span>价：</dt>
								<dd :class="['f-red f-16', goods.promotion ? 'del' : '']">{{ currency(goods.price) }}
								</dd>
							</dl>
							<dl v-if="goods.promotion" class="uni-flex uni-row f-13 f-c55 ml10 mb10">
								<dt>活<span style="margin:0 7px;">动</span>价：</dt>
								<dd class="f-red f-16">{{ currency(goods.promotion.price) }}</dd>
							</dl>
							<dl v-if="goods.fullprefer.amount > 0" class="uni-flex uni-row f-13 f-c55 ml10 mb10">
								<dt>满<span style="margin:0 13px;"></span>减：</dt>
								<dd class="f-red">购满 {{ goods.fullprefer.amount }} 元立减 {{ goods.fullprefer.decrease }}元
								</dd>
							</dl>
							<dl v-if="goods.integral && goods.integral.enabled && goods.integral.exchange_integral > 0"
								class="uni-flex uni-row f-13 f-c55 ml10 mb10">
								<dt>积分抵扣：</dt>
								<dd>可使用{{ goods.integral.exchange_integral }}积分</dd>
							</dl>
							<dl v-if="goods.brand" class="uni-flex uni-row f-13 f-c55 ml10 mb10">
								<dt>品<span style="margin:0 13px;"></span>牌：</dt>
								<dd>{{ goods.brand }}</dd>
							</dl>
						</div>
						<div class="boxes pd10 f-yahei bgf delivery">
							<dl v-if="shipping.name" class="uni-flex uni-row f-13 f-c55 ml10 mb10 vertical-middle">
								<dt>配送费用：</dt>
								<dd class="vertical-middle">
									<span class="border vertical-middle mr10">
										<label class="mr5">至 全国</label>
										<!-- <el-icon :size="12">
											<ArrowDown />
										</el-icon> -->
									</span>
									<span>{{ shipping.name }}：</span>
									<span v-if="shipping.freight > 0">{{ currency(shipping.freight) }}元</span>
									<span v-else>免运费</span>
								</dd>
							</dl>
							<dl class="uni-flex uni-row f-13 f-c55 ml10 mb10">
								<dt>销<span style="margin:0 13px;"></span>量：</dt>
								<dd>已售出{{ goods.sales || 0 }}件</dd>
							</dl>
						</div>
						<div class="boxes pd10 f-yahei bgf specs">
							<dl v-if="specs[fields.field.name]"
								class="uni-flex uni-row f-13 f-c55 ml10 mb10 vertical-middle">
								<dt class="mb10">{{ specs[fields.field.name] }}：</dt>
								<dd class="uni-flex uni-row flex-wrap">
									<p v-on:click="changeSpec(item, fields.field.value, fields.thefield.value)"
										:class="['bgf mr10 mb10 item pointer', specs.disabled[item[fields.field.value]] ? 'disabled' : '', specs.selected[fields.field.value] == item[fields.field.value] ? 'selected' : '']"
										v-for="(item, index) in Object.values(specs[fields.field.value])" :key="index">
										<img v-if="item.image" :src="item.image" :title="item[fields.field.value]" />
										<span v-else
											:class="['pl10 pr10', specs.destroy[item[fields.field.value]] ? 'destroy' : '']">
											{{ item[fields.field.value] }}</span>
									</p>
								</dd>
							</dl>
							<dl v-if="specs[fields.thefield.name]"
								class="uni-flex uni-row f-13 f-c55 ml10 mb20 vertical-middle">
								<dt class="mb10">{{ specs[fields.thefield.name] }}：</dt>
								<dd class="uni-flex uni-row flex-wrap">
									<p v-on:click="changeSpec(item, fields.thefield.value, fields.field.value)"
										:class="['bgf mr10 mb10 item pointer', specs.disabled[item[fields.thefield.value]] ? 'disabled' : '', specs.selected[fields.thefield.value] == item[fields.thefield.value] ? 'selected' : '']"
										v-for="(item, index) in Object.values(specs[fields.thefield.value])" :key="index">
										<span
											:class="['pl10 pr10', specs.destroy[item[fields.thefield.value]] ? 'destroy' : '']">
											{{ item[fields.thefield.value] }}
										</span>
									</p>
								</dd>
							</dl>
							<dl class="uni-flex uni-row f-13 f-c55 ml10 mb10 pt10 vertical-middle">
								<dt>购买数量：</dt>
								<dd class="uni-flex uni-row flex-wrap vertical-middle">
									<el-input-number v-model="cart.quantity" :min="1" :max="Number(selected.stock)" />
									<span class="ml10">库存{{ selected.stock || goods.stock }}件</span>
								</dd>
							</dl>
							<dl v-if="goods.spec_qty > 0"
								class="uni-flex uni-row f-13 f-c55 ml10 mb10 pt10 vertical-middle">
								<dt>您已选择：</dt>
								<dd class="uni-flex uni-row flex-wrap f-red">
									<span>{{ specs.selected[fields.field.value] || '' }}</span>
									<span class="ml5">{{ specs.selected[fields.thefield.value] || '' }}</span>
								</dd>
							</dl>
							<div class="uni-flex uni-row pd10 submit mt20">
								<el-button @click="addCart('buy')" type="danger" size="large">立即购买</el-button>
								<el-button @click="addCart" size="large">加入购物车</el-button>
							</div>
						</div>
					</div>
				</el-col>
			</el-row>
			<el-row>
				<el-col :span="5">
					<div class="historys mb20">
						<div class="hd center pd10 f-16 f-c55 mt10">—— 看了又看 ——</div>
						<ul class="bd center">
							<li v-for="(item) in historys" class="item">
								<router-link :to="'/goods/detail/' + item.goods_id" class="pic mb10 rlink">
									<img :src="item.goods_image" />
								</router-link>
								<router-link :to="'/goods/detail/' + item.goods_id"
									class="desc mb10 rlink f-13 line-clamp-2">{{ item.goods_name }}</router-link>
								<p class="price f-red">{{ currency(item.price) }}</p>
							</li>
						</ul>
					</div>
				</el-col>
				<el-col :span="19">
					<div class="ml20">
						<div class="uni-flex uni-row options mb20 flex-middle">
							<p class="selected">商品详情</p>
							<el-divider direction="vertical" />
							<p><a href="#comment" class="rlink pointer">评价({{ goods.comments || 0 }})</a></p>
							<el-divider direction="vertical" />
							<p>销量({{ goods.sales || 0 }})</p>
						</div>
						<div v-if="goods.attributes.length > 0">
							<el-table :data="goods.attributes" :border="true" :stripe="false" :show-header="false"
								class="mb20">
								<el-table-column prop="name" />
								<el-table-column prop="value" />
							</el-table>
						</div>
						<div class="detail-info pb20 mb20" v-html="goods.description"></div>
						<div v-if="goods.comments > 0" class="comments pb20 mb20">
							<a name="comment"></a>
							<div class="hd bold f-16">评论({{ goods.comments }})</div>
							<div class="bd pb10 mt20">
								<el-timeline>
									<el-timeline-item v-for="(item) in comments.list" :timestamp="item.evaluation_time"
										placement="top">
										<el-card>
											<p class="uni-flex uni-row flex-middle f-13">
												<el-avatar :size="40" :src="item.buyer_portrait" class="mr10" />
												<label class="mr10 bold">
													{{ item.buyer_nickname || item.buyer_name }}</label>
												<el-rate v-model="item.evaluation" disabled />
											</p>
											<p :class="['f-13 mt10', item.comment ? '' : 'f-c55']">
												{{ item.comment || '买家没有留下任何评价' }}
											</p>
											<p v-if="item.reply_comment" class="f-green f-12">
												卖家回复：{{ item.reply_comment }}
											</p>
											<p v-if="item.images.length > 0">
												<img v-for="(image) in item.images" :src="image" width="60" height="60"
													class="mt5 mr5" />
											</p>
											<p v-if="item.specification" class="f-gray mt10 f-12">
												[{{ item.specification }}]</p>
										</el-card>
									</el-timeline-item>
								</el-timeline>
							</div>
							<div v-if="comments.list.length < goods.comments" @click="loadcomments"
								class="fd center f-gray f-13 pointer">加载更多评论</div>
						</div>
					</div>
				</el-col>
			</el-row>
		</div>
	</div>
	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
import { currency, isEmpty, redirect } from '@/common/util.js'
import { goodsRead, goodsImages, goodsSpecs, goodsAttributes, goodsComments } from '@/api/goods.js'
import { storeRead } from '@/api/store.js'
import { storeFullprefer } from '@/api/store.js'
import { collectGoods } from '@/api/favorite.js'
import { cartAdd } from '@/api/cart.js'
import { templateList } from '@/api/delivery.js'

import { Navigation } from 'swiper'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css'
import 'swiper/css/navigation'

import countdown from '@/components/datagrid/countdown.vue'
import myhead from '@/pages/layout/header/home.vue'
import myfoot from '@/pages/layout/footer/home.vue'

const route = useRoute()
const loading = ref(true)
const store = ref({})
const goods = reactive({ integral: {}, images: [], attributes: [], fullprefer: {} })
const specs = reactive({ list: {}, spec_name_1: '', spec_name_2: '', spec_1: {}, spec_2: {}, selected: {}, disabled: {}, destroy: {} })
const fields = reactive({ field: { name: 'spec_name_1', value: 'spec_1' }, thefield: { name: 'spec_name_2', value: 'spec_2' } })
const selected = ref({})
const cart = reactive({ quantity: 1, spec_id: 0 })
const historys = ref([])
const modules = ref([Navigation])
const preview = ref(false)
const closePreview = () => { preview.value = false }
const myswiper = reactive({ selected: 0 })
const comments = reactive({ list: [], page_size: 10 })
const shipping = reactive({})

onMounted(() => {
	goodsRead({ goods_id: route.params.id, querydesc: true, if_show: 1 }, (data) => {
		if (data) {
			Object.assign(goods, data)

			storeRead({ store_id: data.store_id }, (res) => {
				store.value = res
			})
			goodsImages({ goods_id: route.params.id }, (res) => {
				res.list.forEach((item) => {
					goods.images.push(item.thumbnail)
				})
			})
			goodsSpecs({ goods_id: route.params.id }, (res) => {

				// 获取不重复的规格数组
				specs[fields.field.value] = uniqueSpecs(res.list, fields.field.value)

				// 全量规格数组
				specs.list = {}
				res.list.forEach((item, index) => {
					if (index == 0) {
						specs[fields.field.name] = item[fields.field.name]
						specs[fields.thefield.name] = item[fields.thefield.name]

						Object.assign(goods, item)
					}
					specs.list[item.spec_id] = item
				})

				for (let index in specs.list) {
					let item = specs.list[index]
					if (item.stock > 0) {
						changeSpec(item, fields.field.value, fields.thefield.value, true)
						break
					}
				}
			})

			storeFullprefer({ store_id: data.store_id }, (res) => {
				goods.fullprefer = res
			})

			goodsAttributes({ goods_id: data.goods_id }, (res) => {
				goods.attributes = res.list
			})

			// 实物商品才需要配送
			if (goods.type == 'material') {
				templateList({ store_id: data.store_id }, (data) => {
					if (data.list.length > 0) {
						let express = data.list[0].area_fee.express
						shipping.name = express.name
						shipping.freight = express.default_fee.start_fees
					}
				})
			}

			getcomments()
			sethistorys()
		}

	}, loading, true)


})

const collect = (target) => {
	if (target == 'goods') {
		collectGoods({ goods_id: goods.goods_id }, (data) => {
			goods.becollected = true
			ElMessage.success('收藏成功')
		})
	}
}

const slideClick = (swiper, event) => {
	myswiper.selected = swiper.clickedIndex
	if (!isEmpty(goods.images[myswiper.selected])) {
		goods.default_image = goods.images[myswiper.selected]
	}
}

const addCart = (target) => {
	cartAdd(Object.assign(cart, { spec_id: selected.value.spec_id, selected: (target == 'buy') ? 1 : 0 }), (data) => {
		if (target == 'buy') {
			return redirect('/trade/order/normal')
		}
		ElMessage.success('已加入购物车')
	})
}

const loadcomments = () => {
	comments.page_size += 2
	getcomments()
}

function getcomments() {
	goodsComments({ goods_id: goods.goods_id, page_size: comments.page_size }, (data) => {
		comments.list = data.list
		goods.comments = data.statistics.total
	})
}

/**
 * 设置商品浏览历史
 */
function sethistorys() {
	//localStorage.clear()
	historys.value = JSON.parse(localStorage.getItem('historys')) || []
	for (let i = 0; i < historys.value.length; i++) {
		if ((historys.value[i].goods_id == goods.goods_id) || (i > 8)) {
			historys.value.splice(i, 1)
		}
	}

	if (!Array.isArray(historys.value)) historys.value = []
	historys.value.unshift({ goods_id: goods.goods_id, goods_name: goods.goods_name, goods_image: goods.default_image, store_id: goods.store_id, price: goods.price })
	localStorage.setItem('historys', JSON.stringify(historys.value))
}

/**
 * 切换规格后
 * @param {Object} current
 * @param {Object} field
 * @param {Object} thefield
 * @param {Object} autoclick
 */
function changeSpec(current, field, thefield, autoclick) {

	if (specs.disabled[current[field]]) {
		return false
	}

	// 将当前点击的规格设置为选中
	specs.selected[field] = current[field]

	// 选中后，加载第二列的数据
	secondColumn(field, thefield)

	// 不可用的规格暂存数组
	specs.disabled = {}
	specs.destroy = {}

	// 如果只有一个规格
	if (!current[thefield]) {
		for (let index in specs[field]) {
			let item = specs[field][index]
			if (item.stock == 0) {
				specs.disabled[item[field]] = true
			}
		}
		selected.value = current
	}
	//  如果有二个规格
	else {

		// 根据一个规格，筛选另外一个规格的属性列表
		let object = {}
		for (let index in specs.list) {
			let item = specs.list[index]
			if (item[field] == current[field]) {
				object[item[thefield]] = item
			}
		}

		// 把不可用的第二规格做处理
		for (let index in specs[thefield]) {
			let item = specs[thefield][index]
			if (!object[item[thefield]] || object[item[thefield]].stock < 1) {
				specs.disabled[item[thefield]] = true
				specs.destroy[item[thefield]] = !object[item[thefield]] ? true : false

				// 如果上次选中的规格属于不可用的，则删除已选中
				if (item[thefield] == specs.selected[thefield]) {
					delete (specs.selected[thefield])
				}
			}
		}

		selected.value = {}
		for (let index in specs.list) {
			let item = specs.list[index]
			if (specs.selected[field] == item[field] && specs.selected[thefield] == item[
				thefield]) {
				selected.value = item
				break
			}
		}
	}

	Object.assign(goods, selected.value)

	if (!autoclick && current.image) {
		myswiper.selected = -1
		goods.default_image = current.image
	}
}

/**
 * 获取不重复的规格数组
 * @param {Object} list
 * @param {Object} field
 */
function uniqueSpecs(list, field) {
	let object = {}
	for (let index in list) {
		let item = list[index]
		if (isEmpty(object[item[field]])) {
			object[item[field]] = item
		}
	}
	return object
}
/**
 * 点击其中一列，获取另一列的数据
 * @param {Object} field
 * @param {Object} thefield
 */
function secondColumn(field, thefield) {
	let list = []
	for (let index in specs.list) {
		let item = specs.list[index]
		if (item[field] == specs.selected[field]) {
			list[item[thefield]] = item
		}
	}
	Object.assign(specs[thefield], list)
}
</script>

<style scoped>
.carousel .preview {
	border: 1px #ddd solid;
	width: 100%;
	/* height: 400px; */
}

.carousel .preview img {
	width: 100%;
}

.carousel .swiper {
	--swiper-navigation-color: #000000;
	--swiper-navigation-size: 20px;
	padding: 0 40px;
}

.carousel .swiper .swiper-slide img {
	width: calc(100% - 2px);
	height: calc(100% - 2px);
	border: 1px #eee solid;
}

.carousel .swiper .swiper-slide.selected img {
	width: calc(100% - 4px);
	height: calc(100% - 4px);
	border: 2px #eee solid;
	border-color: var(--el-color-danger);
}

:deep() .carousel .swiper .swiper-button-prev,
:deep() .carousel .swiper .swiper-button-next {
	top: 2px;
	width: 40px;
	height: 80px;
	line-height: 80px;
	background-color: #fff;
	text-align: center;
}

:deep() .carousel .swiper .swiper-button-prev {
	left: -5px;
}

:deep() .carousel .swiper .swiper-button-next {
	right: -5px;
}

:deep() .carousel .swiper .swiper-button-lock {
	display: block;
}

.menus {
	background-color: #3E4789;
	height: 40px;
	line-height: 40px;
}

.menus .selected,
.menus li:hover {
	background-color: #000000;
}

.banner {
	background-image: url('@/assets/images/store_banner.jpg');
	background-size: auto 100%;
	background-position: center center;
	background-repeat: no-repeat;
	height: 110px;
}

.promotes {
	background: url('@/assets/images/promotes.jpg') repeat;
}

.boxes {
	background: url('@/assets/images/prices.jpg') repeat;
}

.boxes.bgf {
	background: none;
}

.boxes dt {
	min-width: 70px;
	text-align: right;
}

.specs .item {
	position: relative;
	text-align: center;
	display: block;
	border: 1px solid #ccc;
	white-space: nowrap;
	padding: 3px;
}

.specs .item.selected {
	border: 1px solid var(--el-color-danger);
	color: var(--el-color-danger);
}

.specs .item.disabled {
	border: 1px dotted #ccc;
	color: #ccc;
	cursor: not-allowed;
}

.specs .item .destroy {
	text-decoration: line-through;
}

.specs .item span {
	display: block;
	min-width: 100px;
	padding: 10px 20px;
}

.specs .item img {
	display: block;
	width: 40px;
	height: 40px;
}

:deep() .specs .el-input__inner {
	border-radius: 0;
}

:deep() .specs .el-input-number__increase,
:deep() .specs .el-input-number__decrease,
:deep() .specs .el-input-number__increase.is-disabled,
:deep() .specs .el-input-number__decrease.is-disabled {
	background: none;
}

.delivery .border {
	border: 1px #ddd dashed;
	display: inline-block;
	padding: 2px 8px;
	height: 20px;
	line-height: 20px;
	border-radius: 2px;
	cursor: pointer;
	vertical-align: middle;
}

.submit .el-button {
	padding: 22px 40px;
}

.historys {
	border: 1px #eee solid;
}

.historys li {
	margin: 30px 30px;
}

.historys li img {
	max-width: 100%;
}

.options {
	border: 1px #dddddd solid;
}

.options {
	padding: 15px 0;
}

.options p {
	padding: 0 30px;
}

.options .selected {
	color: var(--el-color-danger);
}

:deep() .detail-info img {
	display: inherit;
	width: 100%;
	max-width: 100%;
}

:deep() .el-rate {
	--el-rate-icon-margin: 0;
}
</style>
