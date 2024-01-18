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
                            <el-breadcrumb-item>货到付款</el-breadcrumb-item>
                        </el-breadcrumb>
                    </div>
                </div>
                <div class="round-edge pd10 bgf mt20">
                    <h3 class="pd10 mb20">开通货到付款</h3>
                    <el-form class="ml10" v-loading="loading">
                        <el-form-item label="支持货到付款的城市列表" :label-width="100">
                            <el-scrollbar height="400px" class="gallery round-edge pl10 pt10 pr10">
                                <el-tree ref="treeRef" v-if="gallery.length > 0" :data="gallery" show-checkbox
                                    node-key="region_id" :default-checked-keys="form.regions" :props="defaultProps"
                                    :default-expand-all="false" />
                            </el-scrollbar>
                        </el-form-item>
                        <el-form-item label="是否启用" :label-width="100">
                            <el-switch v-model="form.status" :active-value="1" :inactive-value="0" />
                        </el-form-item>
                        <el-form-item label=" " :label-width="100">
                            <el-button type="primary" @click="submit" :loading="loading">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </el-col>
        </el-row>
    </div>
    <myfoot></myfoot>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ElMessage, ElTree } from 'element-plus'
import { regionList } from '@/api/region.js'
import { codRead, codUpdate } from '@/api/cod.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const treeRef = ref(ElTree)
const loading = ref(true)
const gallery = ref({})
const form = reactive({ status: 1, regions: [] })

const defaultProps = {
    label: 'name',
    children: 'children'
}

onMounted(() => {
    let visitor = JSON.parse(localStorage.getItem('visitor'))

    regionList({ parent_id: 0, page_size: 50, querychild: true }, (data) => {
        gallery.value = data.list

        codRead({ store_id: visitor.store_id }, (res) => {
            Object.assign(form, res, { status: parseInt(res.status) })
        })

    }, loading)
})

const submit = () => {
    form.regions = treeRef.value.getCheckedKeys(false)
    codUpdate(form, (data) => {
        ElMessage.success('设置成功')
    })
}

</script>
<style scoped>
.gallery {
    background-color: #eee;
    width: calc(90% - 20px);
}

.el-tree {
    background-color: transparent;

}

:deep() .el-form-item__label {
    line-height: 20px;
}
</style>