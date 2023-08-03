<template>
    <myhead :exclude="['imagead']"></myhead>
    <div class="w" v-loading="loading">
        <div class="selectors mt20 bgf pl10 pt10 pr10 pb5 round-edge">
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
            <div class="uni-flex uni-row mt20 mb10 ml20 item">
                <p class="name f-c55">主体类型：</p>
                <div class="uni-flex uni-row flex-wrap values uni-center">
                    <span @click="filters('stype', item)" v-for="(item) in ['company', 'personal']"
                        :class="['bgp mr20 mb10 line-clamp-1 pointer', selectors.stype == item ? 'bgr f-white' : '']">
                        {{ item == 'company' ? '企业' : '个人' }}
                    </span>
                </div>
            </div>
            <div class="uni-flex uni-row mt20 mb20 ml20 item">
                <p class="name f-c55">店铺类型：</p>
                <div class="uni-flex uni-row flex-wrap values uni-center">
                    <span @click="filters('sgrade', item.grade_id)" v-for="(item) in grades"
                        :class="['bgp mr20 mb10 line-clamp-1 pointer', selectors.sgrade == item.grade_id ? 'bgr f-white' : '']">{{
                            item.grade_name }}</span>
                </div>
            </div>
        </div>
        <div class="uni-flex uni-row mt20 bgf pd10 round-edge flex-middle sortby">
            <span class="pt5 pb5 f-gray ml10">排序：</span>
            <div class="uni-flex uni-row pt5 pb5 flex-middle">
                <p :class="['ml20 mr20 pointer', selectors.orderby == '' ? 'f-red' : '']" @click="queryClick('')">综合</p>
                <p :class="['ml20 mr20 pointer', selectors.orderby == 'praise_rate|desc' ? 'f-red' : '']"
                    @click="queryClick('praise_rate|desc')">好评</p>
                <p :class="['ml20 mr20 pointer', selectors.orderby == 'credit_value|desc' ? 'f-red' : '']"
                    @click="queryClick('credit_value|desc')">信誉度</p>
                <p :class="['ml20 mr20 pointer', selectors.orderby == 'add_time|desc' ? 'f-red' : '']"
                    @click="queryClick('add_time|desc')">开店时间</p>
                <p class="uni-flex uni-row vertical-middle ml20">
                    <el-input v-model="selectors.keyword" class="small mr5" maxlength="5" clearable>
                        <template #prepend>关键词</template>
                    </el-input>
                </p>
                <p class="ml10"><el-button @click="queryClick(selectors.orderby)" type="primary" class="f-13">提交</el-button>
                </p>
            </div>
        </div>

        <div v-if="(gallery.list.length > 0)" class="gallery mt20">
            <div class="uni-flex uni-column bgf round-edge pt20">
                <div v-for="(item, index) in gallery.list" class="uni-flex uni-row item">
                    <router-link :to="'/store/index/' + item.store_id" class="rlink">
                        <el-avatar :size="70" :src="item.store_logo" class="bgp" />
                    </router-link>
                    <div class="width-surplus ml20">
                        <div class="uni-flex uni-row">
                            <div class="width-surplus">
                                <p class="uni-flex uni-row vertical-middle">
                                    <em v-if="item.joinway == 1" class="f-12 f-white bgr tag mr5">自营</em>
                                    <em v-else-if="item.stype == 'company'" class="f-12 f-white bgr tag mr5">企业</em>
                                    <router-link :to="'/store/index/' + item.store_id"
                                        class="rlink line-clamp-2 pr10 f-16 f-bold">
                                        {{ item.store_name }}</router-link>
                                    <img :src="item.credit_image" height="16" />
                                </p>
                                <p class="mt5 f-gray f-13 mt10">
                                    {{ Number(item.praise_rate) }}% 好评
                                    <span class="ml20">在售商品数：{{ item.goods_count }}</span>
                                </p>
                                <p class="mt5 f-gray f-13">
                                    地址：{{ item.province || '暂无地址' }}{{ item.city }}{{ item.district }}{{ item.address || '' }}
                                </p>
                            </div>
                            <p class="uni-flex uni-column vertical-middle">
                                <!-- <span class="block f-gray">商品数量：{{ item.goods_count }}</span> -->
                                <router-link :to="'/store/index/' + item.store_id"
                                    class="rlink enter f-white mt20">进店</router-link>
                            </p>
                        </div>
                        <el-divider v-if="gallery.list.length - 1 != index && pagination.page_size != index" />
                    </div>
                </div>
            </div>
            <div v-if="pagination.total > 0" class="mt20 mb20">
                <el-pagination v-model:currentPage="pagination.page" v-model:page-size="pagination.page_size"
                    :background="true" :page-sizes="[10, 40, 100, 200]" layout="total, sizes, prev, pager, next"
                    :total="pagination.total" @size-change="handleSizeChange" @current-change="handleCurrentChange"
                    :hide-on-single-page="false" class="center" />
            </div>
        </div>
        <el-empty v-else-if="!loading" class="round-edge bgf mt20" description="没有店铺" />
    </div>
    <myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { redirect } from '@/common/util.js'
import { storeList, storeGradeList } from '@/api/store.js'

import myhead from '@/pages/layout/header/home.vue'
import myfoot from '@/pages/layout/footer/home.vue'

const loading = ref(true)
const route = useRoute()
const gallery = reactive({ selectors: { by_category: [], by_brand: [], by_props: [] }, filters: [], list: [] })
const selectors = reactive({ orderby: '' })
const grades = ref([])
const pagination = ref({})

onMounted(() => {
    getList()
    storeGradeList(null, (data) => {
        grades.value = data.list
    })
})

const queryClick = (value) => {
    selectors.orderby = value || ''
    getList(selectors)
}

const handleSizeChange = (value) => {
    getList({ page_size: value })
}
const handleCurrentChange = (value) => {
    getList({ page: value, page_size: pagination.value.page_size })
}

const filters = (key, value, action) => {
    selectors[key] = (action == 'remove') ? '' : value
    getList(selectors)
}

function getList(params = {}) {
    storeList(Object.assign({ if_show: 1, closed: 0, cate_id: route.params.id }, route.query, params), (data) => {
        gallery.list = data.list
        console.log(data)
        //gallery.selectors = data.selectors || {}
        //gallery.filters = data.filters || []
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
    padding: 4px 20px 20px 20px;
}

.gallery .item .enter {
    background: var(--my-color-red);
    padding: 5px 20px;
    border-radius: 2px;
    color: #fff;
}

.gallery .item .el-divider--horizontal {
    margin: 24px 0 0 0;
}

.gallery .item .tag {
    border-radius: 3px;
    font-style: normal;
    padding: 2px 8px;
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
    padding: 0 10px;
    font-size: 12px;
}</style>
