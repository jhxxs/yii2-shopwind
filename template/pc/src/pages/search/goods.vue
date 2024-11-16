<template>
    <myhead :exclude="['imagead']"></myhead>
    <div class="w" v-loading="loading">
        <div v-if="gallery.filters.length > 0 || gallery.selectors.by_category.length > 0 || gallery.selectors.by_brand.length > 0 || gallery.selectors.by_props.length > 0"
            class="selectors mt20 bgf pl10 pt10 pr10 pb5 round-edge">
            <div v-if="gallery.filters.length > 0" class="uni-flex uni-row mt10 mb10 ml20 item filters">
                <p class="name f-red">已选：</p>
                <div class="uni-flex uni-row flex-wrap values uni-center flex-middle">
                    <span @click="filters(item.key, item.value, 'remove')" v-for="(item) in gallery.filters"
                        class="bgp mr10 pointer vertical-middle bgr f-white">
                        <label> {{ item.category }}：{{ item.name }}</label>
                        <el-icon>
                            <CloseBold />
                        </el-icon>
                    </span>
                </div>
            </div>
            <div v-if="gallery.selectors.by_category.length > 0" class="uni-flex uni-row mt20 mb10 ml20 item">
                <p class="name f-c55">分类：</p>
                <div class="uni-flex uni-row flex-wrap values uni-center">
                    <span @click="filters('cate_id', item.cate_id)" v-for="(item) in gallery.selectors.by_category"
                        class="bgp mr20 mb10 line-clamp-1 pointer">
                        {{ item.cate_name }}<label class="f-gray">({{ item.count }})</label>
                    </span>
                </div>
            </div>
            <div v-if="gallery.selectors.by_brand.length > 0" class="uni-flex uni-row mt20 mb20 ml20 item">
                <p class="name f-c55">品牌：</p>
                <div class="uni-flex uni-row flex-wrap values uni-center">
                    <span @click="filters('brand', item.brand)" v-for="(item) in gallery.selectors.by_brand"
                        class="bgp mr20 mb10 line-clamp-1 pointer">{{ item.brand }}<label class="f-gray">({{ item.count
                        }})</label></span>
                </div>
            </div>
            <div v-if="gallery.selectors.by_props.length > 0">
                <div v-for="(category) in gallery.selectors.by_props" class="uni-flex uni-row mt20 mb20 ml20 item">
                    <p class="name f-c55">{{ category.name }}：</p>
                    <div class="uni-flex uni-row flex-wrap values uni-center">
                        <span @click="filters('props', item.pid + ':' + item.vid)" v-for="(item) in category.values"
                            class="bgp mr20 mb10 line-clamp-1 pointer">{{
                                item.val
                            }}<label class="f-gray">({{ item.count }})</label></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="uni-flex uni-row mt20 bgf pd10 round-edge flex-middle sortby">
            <span class="pt5 pb5 f-gray ml10">排序：</span>
            <div class="uni-flex uni-row pt5 pb5 flex-middle">
                <p :class="['ml20 mr20 pointer', selectors.orderby == '' ? 'f-red' : '']" @click="queryClick('')">综合</p>
                <p :class="['ml20 mr20 pointer', selectors.orderby == 'sales|desc' ? 'f-red' : '']"
                    @click="queryClick('sales|desc')">销量</p>
                <p :class="['ml20 mr20 pointer', selectors.orderby == 'price|desc' ? 'f-red' : '']"
                    @click="queryClick('price|desc')">价格</p>
                <p :class="['ml20 mr20 pointer', selectors.orderby == 'add_time|desc' ? 'f-red' : '']"
                    @click="queryClick('add_time|desc')">上架</p>
                <p :class="['ml20 mr20 pointer', selectors.orderby == 'comments|desc' ? 'f-red' : '']"
                    @click="queryClick('comments|desc')">评论</p>
                <p :class="['ml20 mr20 pointer', selectors.orderby == 'views|desc' ? 'f-red' : '']"
                    @click="queryClick('views|desc')">人气</p>
                <p class="uni-flex uni-row vertical-middle ml20">
                    <el-input v-model="price.min" class="small mr5" maxlength="5" clearable>
                        <template #prepend>￥</template>
                    </el-input> - <el-input v-model="price.max" class="small ml5" maxlength="5" clearable><template
                            #prepend>￥</template></el-input>
                </p>
                <p class="ml10"><el-button @click="queryClick(selectors.orderby)" type="primary" class="f-13">提交</el-button>
                </p>
            </div>
        </div>

        <div v-if="(gallery.list.length > 0)" class="gallery mt20">
            <div class="uni-flex uni-row flex-wrap bd">
                <div v-for="(item) in gallery.list" class="item bgf pb5 relative">
                    <em v-if="item.promotion" class="f-12 f-white bgr absolute tag">秒杀</em>
                    <router-link :to="'/goods/detail/' + item.goods_id" class="rlink">
                        <img :src="item.default_image" class="block bgp" />
                    </router-link>
                    <router-link :to="'/goods/detail/' + item.goods_id"
                        class="rlink line-clamp-2 pl10 pr10  ml5 mr5 mt5 mb5 desc">{{ item.goods_name
                        }}</router-link>
                    <p class="pl10 pr10 ml5 mr5 pt5 f-gray f-13">
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
                    :background="true" :page-sizes="[10, 40, 100, 200]" layout="total, sizes, prev, pager, next"
                    :total="pagination.total" @size-change="handleSizeChange" @current-change="handleCurrentChange"
                    :hide-on-single-page="false" class="center" />
            </div>
        </div>

        <el-empty v-else-if="!loading" class="round-edge bgf mt20" description="没有商品" />

    </div>

    <myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { currency, redirect } from '@/common/util.js'
import { goodsSearch } from '@/api/goods.js'

import myhead from '@/pages/layout/header/home.vue'
import myfoot from '@/pages/layout/footer/home.vue'

const loading = ref(true)
const route = useRoute()
const gallery = reactive({ selectors: { by_category: [], by_brand: [], by_props: [] }, filters: [], list: [] })
const selectors = reactive({ orderby: '' })
const pagination = ref({})
const price = reactive({ min: '', max: '' })

onMounted(() => {
    getList({ page_size: 40 })
})

const queryClick = (value) => {
    selectors.orderby = value || ''
    if (price.max >= price.min && price.min >= 0) {
        selectors.price = Number(price.min) + '-' + Number(price.max)
    }
    getList(selectors)
}

const handleSizeChange = (value) => {
    getList({ page_size: value })
}
const handleCurrentChange = (value) => {
    getList({ page: value, page_size: pagination.value.page_size })
}

const filters = (key, value, action) => {
    if (key == 'props') {
        if (action == 'remove') {
            value = selectors[key] ? selectors[key].replace(value, '').replace('||', '|') : ''
        } else if (selectors[key] && selectors[key].length > 0) {
            value += '|' + selectors[key]
        }
        selectors[key] = value
    } else {
        selectors[key] = (action == 'remove') ? '' : value
    }
    if (key == 'keyword' && action == 'remove') {
        return redirect('/search/goods')
    }
    getList(selectors)
}

function getList(params = {}) {
    goodsSearch(Object.assign({ if_show: 1, closed: 0, cate_id: route.params.id }, route.query, params), (data) => {
        gallery.list = data.list
        gallery.selectors = data.selectors || {}
        gallery.filters = data.filters || []
        pagination.value = data.pagination
    }, loading)
}

</script>

<style scoped>
.category {
    height: 42px;
    line-height: 42px;
}

.category .rlink {
    color: #fff;
}

.gallery .item {
    width: 224px;
    margin: 0 20px 20px 0;
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

.gallery .item:nth-child(5n) {
    margin-right: 0;
}

.gallery .item:hover {
    box-shadow: 0 3px 20px rgba(0, 0, 0, .06);
}

:deep() .sortby .small .el-input__inner {
    width: 90px;
}

.selectors .item .name {
    padding: 5px;
    min-width: 60px;
}

.selectors .values span {
    border-radius: 4px;
    padding: 5px 20px;
}

:deep() .sortby .el-input-group__append,
:deep() .sortby .el-input-group__prepend {
    padding: 0 5px;
    font-size: 12px;
}
</style>
