<template>
	<myhead :exclude="['category', 'imagead']"></myhead>
	<div v-if="store.store_id" class="main" v-loading="loading">
		<div class="banner" :style="{ 'background-image': store.pcbanner ? 'url(' + store.pcbanner + ')' : '' }"></div>
		<div class="menus">
			<ul class="uni-flex uni-row w f-white">
				<li class="selected pl20 pr20"><span class="pl10 pr10">店铺首页</span></li>
				<li @click="redirect('/store/list/' + store.store_id)" class="pointer pl20 pr20"><span
						class="pl10 pr10">所有商品</span></li>
				<li @click="redirect('/store/list/' + store.store_id + '?orderby=sales|desc')" class="pointer pl20 pr20">
					<span class="pl10 pr10">镇店之宝</span>
				</li>
				<li @click="redirect('/store/list/' + store.store_id + '?orderby=add_time|desc')" class="pointer pl20 pr20">
					<span class="pl10 pr10">新款上市</span>
				</li>
				<li @click="redirect('/store/list/' + store.store_id + '?orderby=views|desc')" class="pointer pl20 pr20">
					<span class="pl10 pr10">人气热款</span>
				</li>
				<li @click="redirect('/store/list/' + store.store_id + '?orderby=comments|desc')" class="pointer pl20 pr20">
					<span class="pl10 pr10">好评返购</span>
				</li>
			</ul>
		</div>
		<div class="w">
			<el-row :gutter="20">
				<el-col :span="5">
					<div class="store bgf mt20 pd10 center l-h20">
						<p class="image mt20 mb10">
							<el-avatar :size="80" :src="store.store_logo" />
						</p>
						<p class="mb20 vertical-middle flex-center">
							<el-tag v-if="store.joinway == 1" effect="dark" size="small" type="danger"
								class="mr5">自营</el-tag>
							<span class="mr10">{{ store.store_name }}</span>
							<img v-if="store.joinway == 0" :src="store.credit_image" />
						</p>
						<div v-if="dynamiceval" class="f-gray f-13 mb20 l-h20">
							<p>
								<label>描述相符：</label>
								<span class="f-blue">
									{{ dynamiceval.goods.value }}
								</span>
							</p>
							<p>
								<label>服务评分：</label>
								<span class="f-blue">
									{{ dynamiceval.service.value }}
								</span>
							</p>
							<p>
								<label>物流评分：</label>
								<span class="f-blue">
									{{ dynamiceval.shipped.value }}
								</span>
							</p>
						</div>
						<p class="f-13 pt10 bt f-c55">电话：{{ store.tel || '-' }}</p>
						<p class="f-13 pb10 f-c55 pt5">地址：{{ store.city }}{{ store.district }}{{ store.address }}</p>
						<p class="pt10 bt">
							<el-button @click="collect(store.store_id)" class="f-13 mt5" type="primary"
								:plain="!store.becollected">
								<el-icon v-if="store.becollected"><Select /></el-icon>
								<span>关注店铺</span>
							</el-button>
						</p>
					</div>

					<div v-if="(scategory.length > 0)" class="bgf mt20 pd10">
						<div class="hd center pd10 f-c55">—— 商品分类 ——</div>
						<el-menu :unique-opened="true">
							<el-sub-menu v-for="(category) in scategory">
								<template #title>
									<router-link :to="'/store/list/' + category.store_id + '/' + category.cate_id" class="rlink f-12">
										<el-icon :size="12"><ArrowRight /></el-icon>{{ category.cate_name }}
									</router-link>
								</template>
								<el-menu-item v-for="(item) in category.children">
									<router-link :to="'/store/list/' + item.store_id + '/' + item.cate_id" class="rlink f-12">
										<el-icon :size="12"><ArrowRight /></el-icon>{{ item.cate_name }}
									</router-link>
								</el-menu-item>
							</el-sub-menu>
						</el-menu>
					</div>

					<div v-if="(recommends.length > 0)" class="recommends bgf mt20 pd10">
						<div class="hd center pd10 f-c55">—— 人气热卖 ——</div>
						<div v-for="(item) in recommends" class="uni-flex uni-row item mt10 pd10">
							<router-link :to="'/goods/detail/' + item.goods_id" class="rlink">
								<img :src="item.default_image" />
							</router-link>
							<div class="ml10">
								<router-link :to="'/goods/detail/' + item.goods_id" class="rlink line-clamp-1 f-13 mb5"
									:title="item.goods_name">{{ item.goods_name }}</router-link>
								<p class="f-12 f-gray mb5">已售出{{ item.sales }}件</p>
								<p v-if="item.promotion" class="f-red">
									{{ currency(item.promotion.price) }}
								</p>
								<p v-else class="f-red">{{ currency(item.price) }}</p>
							</div>
						</div>
					</div>
				</el-col>
				<el-col :span="19">
					<div v-if="swiper.length > 0" class="swiper mt20">
						<el-carousel height="400px">
							<el-carousel-item v-for="(item, index) in swiper">
								<img @click="redirect(item.link)" :src="item.url" class="pointer" />
							</el-carousel-item>
						</el-carousel>
					</div>
					<div v-if="(gallery.length > 0)" class="gallery mt20">
						<div v-if="swiper.length > 0" class="hd flex-middle center mt20 mb20">
							<h3 class="f-18 f-yahei pl20 pr20 mt10 mb10 relative">店铺推荐</h3>
						</div>
						<div class="uni-flex uni-row flex-wrap bd">
							<div v-for="(item) in gallery" class="item bgf pb5 relative">
								<em v-if="item.promotion" class="f-12 f-white bgr absolute tag">秒杀</em>
								<router-link :to="'/goods/detail/' + item.goods_id" class="rlink">
									<img :src="item.default_image" class="block bgp" />
								</router-link>
								<router-link :to="'/goods/detail/' + item.goods_id"
									class="rlink line-clamp-2 pl10 pr10 mt5 mb5 f-13 center desc">{{
										item.goods_name
									}}</router-link>
								<p v-if="item.promotion" class="center f-red pd10">
									<del class="f-gray mr10">{{ currency(item.price) }}</del>
									<span>{{ currency(item.promotion.price) }}</span>
								</p>
								<p v-else class="center f-red pd10">{{ currency(item.price) }}</p>
							</div>
						</div>
						<div v-if="(pagination.page_count > 1)" class="fd center mt10">
							<router-link :to="'/store/list/' + store.store_id" class="rlink pd10 f-c55">查看更多</router-link>
						</div>
					</div>
					<div v-if="gallery.length == 0 && !loading" class="w bgf mt20 center">
						<el-empty description="没有相关商品"></el-empty>
					</div>
				</el-col>
			</el-row>
		</div>
	</div>
	<div v-else-if="!loading">
		<div class="w bgf mt20 center">
			<el-empty description="店铺不存在"></el-empty>
		</div>
	</div>
	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
import { currency, redirect } from '@/common/util.js'
import { goodsList } from '@/api/goods.js'
import { storeRead, storeSwiper, storeDynamiceval } from '@/api/store.js'
import { categoryList } from '@/api/category.js'
import { collectStore } from '@/api/favorite.js'

import myhead from '@/pages/layout/header/home.vue'
import myfoot from '@/pages/layout/footer/home.vue'

const loading = ref(true)
const route = useRoute()
const gallery = ref([])
const pagination = ref({})
const recommends = ref([])
const store = ref({})
const swiper = ref([])
const scategory = ref([])
const dynamiceval = ref(null)

onMounted(() => {
	storeRead({ store_id: route.params.id }, (data) => {
		store.value = data
	}, loading, true)

	storeSwiper({ store_id: route.params.id }, (data) => {
		swiper.value = data
	})

	storeDynamiceval({ store_id: route.params.id }, (data) => {
		dynamiceval.value = data
	})

	// 不展示店铺商品分类筛选（保留，有需要可开启）
	// categoryList({ store_id: route.params.id, querychild: true, if_show: 1, parent_id: 0, page_size: 50 }, (data) => {
	// 	scategory.value = data.list
	// })

	goodsList({ store_id: route.params.id, orderby: 'add_time|desc', page_size: 20 }, (data) => {
		gallery.value = data.list
		pagination.value = data.pagination
	})

	goodsList({ store_id: route.params.id, orderby: 'sales|desc', page_size: 10 }, (data) => {
		recommends.value = data.list
	})
})

const collect = (value) => {
	collectStore({ store_id: value, remove: store.value.becollected ? true : false }, (data) => {
		if (data) {
			store.value.becollected = !store.value.becollected
			ElMessage.success(store.value.becollected == 1 ? "已关注店铺" : "已取消关注")
		}
	})
}

</script>

<style scoped>
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

.store {
	padding-left: 30px;
	padding-right: 30px;
	padding-bottom: 22px;
}

.store .bt {
	border-top: 1px #ccc dotted;
}

.swiper img {
	width: 100%;
	height: 100%;
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

.gallery .item {
	width: 222px;
	margin: 0 19px 19px 0;
	border-radius: 4px;
}

.gallery .item img {
	width: 100%;
	border-radius: 4px 4px 0 0;
}

.gallery .item .tag {
	top: 0;
	left: 0;
	border-radius: 4px 0 10px 0;
	font-style: normal;
	padding: 2px 8px;
}

.gallery .item .desc {
	min-height: 32px;
}

.gallery .item:nth-child(4n) {
	margin-right: 0;
}

.gallery .item:hover {
	box-shadow: 0 3px 20px rgba(0, 0, 0, .06);
}

.recommends .item img {
	width: 60px;
	height: 60px;
}

:deep() .el-menu {
	border-right: 0;
	--el-menu-item-height: 50px;
}
</style>
