<template>
    <myhead></myhead>
    <div class="main w pt10">
        <el-row :gutter="12">
            <el-col :span="4">
                <menus></menus>
            </el-col>
            <el-col :span="20">
                <div class="round-edge pd10 bgf">
                    <div class="pd10">
                        <el-breadcrumb separator="/">
                            <el-breadcrumb-item>商品</el-breadcrumb-item>
                            <el-breadcrumb-item>库存预警</el-breadcrumb-item>
                        </el-breadcrumb>
                    </div>
                </div>
                <div class="round-edge pd10 bgf mt20">
                    <div class="pl10 pt10">
                        <el-form :inline="true">
                            <el-form-item label="商品名称">
                                <el-input v-model="form.keyword" clearable />
                            </el-form-item>
                            <el-form-item label="品牌">
                                <el-input v-model="form.brand" clearable />
                            </el-form-item>
                            <el-form-item>
                                <el-button @click="queryClick" type="primary" class="f-13">查询</el-button>
                            </el-form-item>
                        </el-form>
                    </div>
                </div>
                <div class="round-edge pd10 bgf mt20" v-loading="loading">
                    <el-table :data="gallery" :border="false" :stripe="false">
                        <el-table-column type="selection" />
                        <el-table-column width="70" label="图片" align="center">
                            <template #default="scope">
                                <router-link :to="'/goods/detail/' + scope.row.goods_id" class="rlink">
                                    <img :src="scope.row.goods_image" width="50" height="50" />
                                </router-link>
                            </template>
                        </el-table-column>
                        <el-table-column label="商品名称">
                            <template #default="scope">
                                <p class="l-h17"><router-link :to="'/goods/detail/' + scope.row.goods_id"
                                        class="rlink">{{
                                        (scope.row.goods_name) }}</router-link></p>
                            </template>
                        </el-table-column>
                        <el-table-column prop="brand" label="品牌" width="100" />
                        <el-table-column prop="stocks" label="库存总量" width="100" sortable />
                        <el-table-column fixed="right" label="操作" width="140" align="center">
                            <template #default="scope">
                                <el-button type="primary" size="small"
                                    @click="redirect('/seller/goods/build/' + scope.row.goods_id)" plain>调整库存
                                </el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div v-if="pagination.total > 0" class="mt20 mb20">
                        <el-pagination v-model:currentPage="pagination.page" v-model:page-size="pagination.page_size"
                            :page-sizes="[10, 50, 100, 200]" :background="false"
                            layout="total, sizes, prev, pager, next" :total="pagination.total"
                            @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :hide-on-single-page="false" class="center" />
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>

    <myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { goodsStockwarn } from '@/api/goods.js'
import { currency, redirect } from '@/common/util.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const loading = ref(false)
const gallery = ref([])
const pagination = ref({})
const form = reactive({ orderby: 'add_time|desc' })
const visitor = ref({})

onMounted(() => {
    visitor.value = JSON.parse(localStorage.getItem('visitor'))
    getList()
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

function getList(params) {
    goodsStockwarn(Object.assign(form, params, { store_id: visitor.value.store_id }), (data) => {
        gallery.value = data.list
        pagination.value = data.pagination
    }, loading)
}
</script>

<style scoped>
.el-table,
.el-form-item {
    font-size: 13px;
}

:deep() .el-table__header-wrapper .el-table-column--selection .el-checkbox {
    vertical-align: middle;
}
</style>
