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
                    <h3 class="pd10 mb20">店招设置</h3>
                    <el-form :inline="true" class="pd10">

                        <el-form-item label="上传店招" :label-width="100">
                            <el-upload action="#" :show-file-list="false" :auto-upload="false" :on-change="fileUpload">
                                <p v-if="store.pcbanner" class="relative image"
                                    :style="{ 'background-image': 'url(' + store.pcbanner + ')' }">
                                    <el-icon class="absolute" :size="20" @click.stop="remove()">
                                        <CircleCloseFilled />
                                    </el-icon>
                                </p>
                                <el-icon v-else class="image" :size="20">
                                    <plus />
                                </el-icon>
                            </el-upload>
                        </el-form-item>
                        <el-form-item label=" " :label-width="100">
                            <p class="f-gray f-12">注：建议尺寸1920PX*110PX</p>
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
import { ElMessage, ElNotification, ElMessageBox } from 'element-plus'
import { storeRead, storeUpdate } from '@/api/store.js'
import { uploadFile } from '@/api/upload.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const loading = ref(false)
const visitor = ref({})
const store = ref({})

onMounted(() => {
    visitor.value = JSON.parse(localStorage.getItem('visitor')) || {}
    storeRead({ store_id: visitor.value.store_id }, (data) => {
        store.value = data
    })
})

const remove = () => {
    ElMessageBox.confirm('您确定要删除店招吗？', '提示', {
        confirmButtonText: '确定',
        type: 'warning'
    }).then(() => {
        storeUpdate({ 'pcbanner': '' }, (data) => {
            store.value.pcbanner = ''
            ElMessage.success('删除成功！')
        })
    }).catch(() => { })
}
const fileUpload = (file) => {
    uploadFile(file.raw, { store_id: visitor.value.store_id, folder: 'other/' }, (data) => {
        store.value.pcbanner = data.fileUrl
        storeUpdate({ 'pcbanner': data.fileUrl }, (data) => {
            ElNotification({
                title: '提示',
                message: '店招设置成功！',
                type: 'success',
                position: 'bottom-left'
            })
        }, loading)
    })
}

</script>
<style scoped>
.el-form .image {
    width: 700px;
    border: 1px #ccc dotted;
    padding: 50px;
    border-radius: 4px;
    background-size: contain;
    background-position: center center;
    background-repeat: no-repeat;
    height: 50px;
}

.el-form .absolute {
    top: -15px;
    right: -10px;
    z-index: 99;
}
</style>