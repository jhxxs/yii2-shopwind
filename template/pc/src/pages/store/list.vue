<template>
    <myhead :exclude="['category', 'imagead']"></myhead>
    <div v-if="store.store_id" class="main" v-loading="loading">
        <div class="banner" :style="{ 'background-image': store.pcbanner ? 'url(' + store.pcbanner + ')' : '' }"></div>
        <div class="menus">
            <ul class="uni-flex uni-row w f-white">
                <li @click="redirect('/store/index/' + store.store_id)" class="pointer pl20 pr20"><span
                        class="pl10 pr10">店铺首页</span></li>
                <li @click="redirect('/store/list/' + store.store_id)"
                    :class="['pointer pl20 pr20', !form.orderby ? 'selected' : '']"><span class="pl10 pr10">所有商品</span>
                </li>
                <li @click="redirect('/store/list/' + store.store_id + '?orderby=sales|desc')"
                    :class="['pointer pl20 pr20', form.orderby == 'sales|desc' ? 'selected' : '']">
                    <span class="pl10 pr10">镇店之宝</span>
                </li>
                <li @click="redirect('/store/list/' + store.store_id + '?orderby=add_time|desc')"
                    :class="['pointer pl20 pr20', form.orderby == 'add_time|desc' ? 'selected' : '']"><span
                        class="pl10 pr10">新款上市</span></li>
                <li @click="redirect('/store/list/' + store.store_id + '?orderby=views|desc')"
                    :class="['pointer pl20 pr20', form.orderby == 'views|desc' ? 'selected' : '']">
                    <span class="pl10 pr10">人气热款</span>
                </li>
                <li @click="redirect('/store/list/' + store.store_id + '?orderby=comments|desc')"
                    :class="['pointer pl20 pr20', form.orderby == 'comments|desc' ? 'selected' : '']"><span
                        class="pl10 pr10">好评返购</span></li>
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
                    <div class="selectors mt20 bgf pd10">
                        <el-form :inline="true" class="pt10 ml10">
                            <el-form-item label="排序">
                                <el-select v-model="form.orderby" @change="queryClick" placeholder="不限制" clearable>
                                    <el-option label="销量从高到低" value="sales|desc" />
                                    <el-option label="点击量从高到低" value="views|desc" />
                                    <el-option label="价格从高到低" value="price|desc" />
                                    <el-option label="价格从低到高" value="price|asc" />
                                    <el-option label="上架时间从近到远" value="add_time|desc" />
                                    <el-option label="上架时间从远到近" value="add_time|asc" />
                                    <el-option label="评论数从多到少" value="comments|desc" />
                                </el-select>
                            </el-form-item>
                            <el-form-item label="商家推荐">
                                <el-select v-model="form.recommended" @change="queryClick" placeholder="不限制" clearable>
                                    <el-option label="是" value="1" />
                                </el-select>
                            </el-form-item>
                            <el-form-item label="商品名称">
                                <el-input v-model="form.keyword" clearable />
                            </el-form-item>
                            <div class="block">
                                <el-form-item label="品牌">
                                    <el-input v-model="form.brand" clearable />
                                </el-form-item>
                                <el-form-item>
                                    <el-button @click="queryClick" type="primary" class="f-13">查询</el-button>
                                </el-form-item>
                            </div>
                        </el-form>
                    </div>
                    <div v-if="(gallery.length > 0)" class="gallery mt20">
                        <div class="uni-flex uni-row flex-wrap bd">
                            <div v-for="(item) in gallery" class="item bgf pb5 relative">
                                <em v-if="item.promotion" class="f-12 f-white bgr absolute tag">秒杀</em>
                                <router-link :to="'/goods/detail/' + item.goods_id" class="rlink">
                                    <img :src="item.default_image" class="block bgp" />
                                </router-link>
                                <router-link :to="'/goods/detail/' + item.goods_id"
                                    class="rlink line-clamp-2 pl10 pr10 ml5 mr5 mt5 mb5 f-13 desc">{{
                                        item.goods_name
                                    }}</router-link>
                                <p class="pl10 pr10 pt5 ml5 mr5 f-gray f-13">
                                    已售出: {{ item.sales || 0 }} 件
                                </p>
                                <p v-if="item.promotion" class="f-16 f-red pd10 ml5 mr5">
                                    <del class="f-gray mr10">{{ currency(item.price) }}</del>
                                    <span>{{ currency(item.promotion.price) }}</span>
                                </p>
                                <p v-else class="f-16 f-red pd10 ml5 mr5">{{ currency(item.price) }}</p>
                            </div>
                        </div>
                        <div v-if="pagination.total > 0" class="mt20 mb20">
                            <el-pagination v-model:currentPage="pagination.page" v-model:page-size="pagination.page_size"
                                :background="true" layout="total, prev, pager, next" :total="pagination.total"
                                @size-change="handleSizeChange" @current-change="handleCurrentChange"
                                :hide-on-single-page="false" class="center" />
                        </div>
                    </div>
                    <div v-else-if="!loading">
                        <div class="w bgf mt10 center" style="padding: 50px 0">
                            <el-icon class="f-blue" :size="100">
                                <WarningFilled />
                            </el-icon>
                            <p class="mt10 f-c55">没有商品</p>
                        </div>
                    </div>
                </el-col>
            </el-row>
        </div>
    </div>
    <div v-else-if="!loading">
        <div class="w bgf mt10 center" style="padding: 50px 0">
            <el-icon class="f-blue" :size="100">
                <WarningFilled />
            </el-icon>
            <p class="mt10 f-c55">店铺不存在</p>
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
import { storeRead, storeDynamiceval } from '@/api/store.js'
import { collectStore } from '@/api/favorite.js'

import myhead from '@/pages/layout/header/home.vue'
import myfoot from '@/pages/layout/footer/home.vue'

const loading = ref(true)
const route = useRoute()
const gallery = ref([])
const pagination = ref({})
const recommends = ref([])
const store = ref({})
const dynamiceval = ref(null)
const form = reactive({ page_size: 20, orderby: '' })

onMounted(() => {
    Object.assign(form, { scate_id: route.params.sid }, route.query)
    storeRead({ store_id: route.params.id }, (data) => {
        store.value = data
        getList()
    }, loading, true)

    storeDynamiceval({ store_id: route.params.id }, (data) => {
        dynamiceval.value = data
    })

    goodsList({ store_id: route.params.id, orderby: 'sales|desc', page_size: 10 }, (data) => {
        recommends.value = data.list
    })
})

const queryClick = () => {
    getList()
}

const handleSizeChange = (value) => {
    getList({ page_size: value })
}
const handleCurrentChange = (value) => {
    getList({ page: value, page_size: pagination.value.page_size })
}

const collect = (value) => {
    collectStore({ store_id: value }, (data) => {
        if (data) {
            store.value.becollected = true
            ElMessage.success('已关注该店铺')
        }
    })
}

function getList(params = {}) {
    goodsList(Object.assign(form, { store_id: route.params.id }, params), (data) => {
        gallery.value = data.list
        pagination.value = data.pagination
    }, loading)
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
</style>
