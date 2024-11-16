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
                            <el-breadcrumb-item>店铺</el-breadcrumb-item>
                            <el-breadcrumb-item>商品分类</el-breadcrumb-item>
                        </el-breadcrumb>
                    </div>
                </div>
                <div class="round-edge pd10 bgf mt20">
                    <h3 class="pd10 mb20">商品分类</h3>

                    <el-tree v-if="gallery.length > 0" ref="treeRef" :data="gallery" node-key="id" :props="{ label: 'value' }"
                        :default-expand-all="true" show-checkbox v-loading="loading" :expand-on-click-node="false"
                        empty-text="">
                        <template #default="{ node, data }">
                            <div class="uni-flex uni-row w-full">
                                <p class="width-surplus">{{ node.label }}</p>
                                <p class="mr10">
                                    <a v-if="data.parent_id == 0" @click="addClick(data)" class="rlink f-blue">新增下级</a>
                                    <a @click="modifyClick(data)" class="rlink f-blue ml20">编辑</a>
                                    <a @click="deleteClick(data)" class="rlink f-blue ml20">删除</a>
                                </p>
                            </div>
                        </template>
                    </el-tree>
                    <div class="pd10 mb20">
                        <el-button v-if="gallery.length > 0" class="ml20 mr20" type="warning" @click="deleteClick()" plain>
                            选中项批量删除
                        </el-button>
                        <el-button v-if="!loading" type="primary" @click="addClick()" plain>
                            新增分类
                        </el-button>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>

    <categorybuild :title="dialog.title" :visible="dialog.visible" :data="dialog.data" @close="dialogClose">
    </categorybuild>

    <myfoot></myfoot>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ElMessage, ElMessageBox, ElTree } from 'element-plus'
import { categoryTree, categoryRead, categoryDelete } from '@/api/category.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'
import categorybuild from '@/components/dialog/category/build.vue'

const treeRef = ref(ElTree)
const loading = ref(true)
const gallery = ref([])
const visitor = ref({})
const dialog = reactive({ title: '新增分类', visible: false, data: {} })

onMounted(() => {
    visitor.value = JSON.parse(localStorage.getItem('visitor'))
    getTree()
})

const addClick = (value) => {
    dialog.visible = true
    dialog.data = value ? { parent_id: parseInt(value.id) } : {}
}
const modifyClick = (value) => {
    dialog.visible = true
    dialog.title = '编辑分类'

    categoryRead({ cate_id: value.id }, (data) => {
        dialog.data = data || {}
    })
}
const deleteClick = (value) => {
    ElMessageBox.confirm('您确定要删除当前分类及下面子分类吗？', '提示', {
        confirmButtonText: '确定',
        type: 'warning'
    }).then(() => {
        let cate_id = value ? value.id : (treeRef.value ? treeRef.value.getCheckedKeys(false).join(',') : 0)

        categoryDelete({ cate_id: cate_id }, (data) => {
            ElMessage.success('删除成功')
            getTree()
        })
    }).catch(() => { })
}

const dialogClose = (value) => {
    dialog.visible = false
    if (value) {
        getTree()
    }
}

function getTree() {
    categoryTree({ store_id: visitor.value.store_id }, (data) => {
        gallery.value = data
    }, loading)
}

</script>
<style scoped>
.el-tree {
    padding: 0 20px 20px 20px;
    line-height: 25px;
}

:deep() .el-tree-node__content {
    height: 45px;
    border-bottom: 1px #f1f1f1 solid;
}
</style>