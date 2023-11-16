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
                            <el-breadcrumb-item>店铺设置</el-breadcrumb-item>
                        </el-breadcrumb>
                    </div>
                </div>
                <div class="round-edge pd10 bgf mt20">
                    <h3 class="pd10 mb20">基本设置</h3>
                    <el-form :inline="true" class="pd10">
                        <el-form-item label="店铺LOGO" :label-width="100">
                            <el-upload action="#" :show-file-list="false" :auto-upload="false" :on-change="fileUpload">
                                <img v-if="store.store_logo" width="120" height="120"
                                    :src="store.store_logo + '?t=' + random" />
                                <el-icon v-else class="image" :size="20">
                                    <plus />
                                </el-icon>
                            </el-upload>
                        </el-form-item>
                        <el-form-item label="店铺名称" :label-width="100">
                            <el-input v-model="store.store_name" />
                        </el-form-item>
                        <el-form-item v-if="store.store_name" label="所在地区" :label-width="100">
                            <multiselector api="region/list" idField="region_id" nameField="region_name"
                                parentField="parent_id"
                                :original="[store.province || '', store.city || '', store.district || '']"
                                @callback="(data) => { callback(data, 'region_id') }">
                            </multiselector>
                        </el-form-item>
                        <el-form-item label="详细地址" :label-width="100">
                            <el-input v-model="store.address" clearable />
                        </el-form-item>
                        <el-form-item label="联系QQ" :label-width="100">
                            <el-input v-model="store.qq" clearable />
                        </el-form-item>
                        <el-form-item label="联系电话" :label-width="100">
                            <el-input v-model="store.tel" clearable />
                        </el-form-item>
                        <el-form-item label="店铺介绍" :label-width="100">
                            <el-input v-model="store.description" type="textarea" />
                        </el-form-item>
                        <el-form-item label=" " :label-width="100">
                            <el-button type="primary" @click="submit" :loading="loading">提交</el-button>
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
import { ElMessage, ElNotification } from 'element-plus'
import { storeRead, storeUpdate } from '@/api/store.js'
import { uploadFile } from '@/api/upload.js'

import multiselector from '@/components/selector/multiselector.vue'
import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const loading = ref(false)
const visitor = ref({})
const store = reactive({})
const random = ref(Math.random())

onMounted(() => {
    visitor.value = JSON.parse(localStorage.getItem('visitor'))
    storeRead({ store_id: visitor.value.store_id }, (data) => {
        ['store_logo', 'store_name', 'region_id', 'address', 'description', 'tel', 'qq', 'province', 'city', 'district'].forEach((field) => {
            store[field] = data[field]
        })
    })
})

const callback = (value) => {
    store.region_id = value.id
}

const submit = () => {
    storeUpdate(store, (data) => {
        ElNotification({
            title: '提示',
            message: '店铺信息修改成功！',
            type: 'success',
            position: 'bottom-left'
        })
    }, loading)
}
const fileUpload = (file) => {
    uploadFile(file.raw, { filename: 'store_logo', store_id: visitor.value.store_id, folder: 'other/' }, (data) => {
        store.store_logo = data.fileUrl
        storeUpdate({ store_logo: data.fileUrl }, () => {
            random.value = Math.random()
            ElMessage.success('店铺LOGO已修改！')
        })
    })
}

</script>
<style scoped>
.el-form .el-form-item {
    margin-right: 40%;
}

.el-form .el-input,
.el-form .el-textarea,
.el-form .el-select {
    width: 220px;
}

.el-form .image {
    border: 1px #ccc dotted;
    padding: 50px;
    border-radius: 4px;
}

.el-form img {
    border-radius: 4px;
}
:deep() .el-form .el-select {
    margin-bottom: 10px;
}
</style>