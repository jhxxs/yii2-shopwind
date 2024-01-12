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
                            <el-breadcrumb-item>物流</el-breadcrumb-item>
                            <el-breadcrumb-item>收货地址</el-breadcrumb-item>
                        </el-breadcrumb>
                    </div>
                </div>
                <div class="round-edge pd10 bgf mt20" v-loading="loading">
                    <div class="mb10">
                        <el-button type="primary" @click="addClick">添加新地址</el-button>
                    </div>
                    <el-table :data="gallery" :border="false" :stripe="false" class="f-13">
                        <el-table-column type="selection" />
                        <el-table-column width="60" label="默认">
                            <template #default="scope">
                                <el-icon class="f-green f-14" v-if="scope.row.defaddr == 1">
                                    <SuccessFilled />
                                </el-icon>
                            </template>
                        </el-table-column>
                        <el-table-column prop="consignee" width="100" label="收货人姓名" />
                        <el-table-column label="所在地区" width="250">
                            <template #default="scope">
                                {{ scope.row.province }}{{ scope.row.city }}{{ scope.row.district || '' }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="address" label="详细地址" width="240" />
                        <el-table-column prop="phone_mob" label="手机号" width="150" sortable />
                        <el-table-column prop="phone_tel" label="固定电话" width="150" sortable />
                        <el-table-column fixed="right" label="操作" width="120" align="center">
                            <template #default="scope">
                                <el-button class="f-13 mr5" type="text" @click="modifyClick(scope.$index)">编辑
                                </el-button>
                                <el-button class="f-13" type="text" @click="deleteClick(scope.$index)">删除
                                </el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div v-if="pagination.total > 0" class="mt20 mb20">
                        <el-pagination v-model:currentPage="pagination.page" v-model:page-size="pagination.page_size"
                            :page-sizes="[10, 50, 100, 200]" :background="false" layout="total, sizes, prev, pager, next"
                            :total="pagination.total" @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :hide-on-single-page="false" class="center" />
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>

    <addressbuild :title="dialog.title" :visible="dialog.visible" :data="dialog.data" @close="dialogClose">
    </addressbuild>

    <myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ElMessageBox, ElMessage } from 'element-plus'
import { redirect } from '@/common/util.js'
import { addressList, addressDelete } from '@/api/address.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'
import addressbuild from '@/components/dialog/address/build.vue'

const route = useRoute()
const loading = ref(false)
const gallery = ref([])
const pagination = ref({})
const modifyIndex = ref(0)
const dialog = reactive({ visible: false })

onMounted(() => {
    getList()
})

const addClick = () => {
    dialog.title = '添加收货地址'
    dialog.visible = true
    dialog.data = {}
}
const modifyClick = (value) => {
    dialog.title = '编辑收货地址'
    dialog.visible = true
    dialog.data = gallery.value[value]
    modifyIndex.value = value
}
const deleteClick = (value) => {
    ElMessageBox.confirm('您确定要删除该条记录吗？', '提示', {
        confirmButtonText: '确定',
        type: 'warning'
    }).then(() => {
        addressDelete(gallery.value[value], (data) => {
            ElMessage.success('删除成功！')
            gallery.value.splice(value, 1)
        })
    }).catch(() => { })
}

const dialogClose = (value, mode) => {
    dialog.visible = false

    if (value && value.consignee) {
        if (mode == 'update') {
            Object.assign(gallery.value[modifyIndex.value] ?? {}, value)
        } else gallery.value.unshift(value)

        if (route.query.redirect) {
            redirect(route.query.redirect)
        }
    }
}

const handleSizeChange = (value) => {
    getList({ page_size: value })
}
const handleCurrentChange = (value) => {
    getList({ page: value, page_size: pagination.value.page_size })
}
function getList(params) {
    addressList(params, (data) => {
        gallery.value = data.list
        pagination.value = data.pagination
    }, loading)
}
</script>
