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
                    <h3 class="pd10 mb20">轮播图设置</h3>
                    <el-form :inline="true" class="pd10">
                        <div v-for="(item, index) in swiper" :key="index" class="mb20">
                            <el-form-item label="上传图片" :label-width="100">
                                <el-upload action="#" :show-file-list="false" :auto-upload="false"
                                    :on-change="(file) => { fileUpload(file, index) }">
                                    <p v-if="item.url" class="relative">
                                        <img width="500" height="220" :src="item.url" />
                                        <el-icon class="absolute" :size="20" @click.stop="remove(index)">
                                            <CircleCloseFilled />
                                        </el-icon>
                                    </p>
                                    <el-icon v-else class="image" :size="20">
                                        <plus />
                                    </el-icon>
                                </el-upload>
                            </el-form-item>
                            <el-form-item label="链接地址" :label-width="100">
                                <el-input v-model="item.link" clearable />
                            </el-form-item>
                        </div>
                        <div class="mb20">
                            <el-form-item label=" " :label-width="100">
                                <el-upload action="#" :show-file-list="false" :auto-upload="false"
                                    :on-change="(file) => { fileUpload(file, swiper.length) }">
                                    <el-icon class="image" :size="20">
                                        <plus />
                                    </el-icon>
                                </el-upload>
                                <p class="f-13 f-gray ml10 l-h17">建议图片大小300KB以内<br />尺寸：950像素*425像素</p>
                            </el-form-item>
                        </div>
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
import { ElNotification } from 'element-plus'
import { storeSwiper, storeUpdate } from '@/api/store.js'
import { uploadFile } from '@/api/upload.js'
import { isEmpty } from '@/common/util.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const loading = ref(false)
const visitor = ref({})
const swiper = ref({})

onMounted(() => {
    visitor.value = JSON.parse(localStorage.getItem('visitor'))
    storeSwiper({ store_id: visitor.value.store_id }, (data) => {
        swiper.value = data
    })
})

const submit = () => {
    storeUpdate({ swiper: swiper.value }, (data) => {
        ElNotification({
            title: '提示',
            message: '店铺轮播设置成功！',
            type: 'success',
            position: 'bottom-left'
        })
    }, loading)
}
const remove = (index) => {
    if (!isEmpty(swiper.value[index])) {
        (swiper.value).splice(index, 1)
    }
}
const fileUpload = (file, index) => {
    uploadFile(file.raw, { store_id: visitor.value.store_id, folder: 'swiper/', filename: index + 1 }, (data) => {
        if (isEmpty(swiper.value[index])) {
            swiper.value[index] = { link: '' }
        }
        swiper.value[index].url = data.fileUrl
    })
}

</script>
<style scoped>
.el-form .el-form-item {
    margin-right: 40%;
}

.el-form .el-input {
    width: 500px;
}

.el-form .image {
    border: 1px #ccc dotted;
    padding: 50px;
    border-radius: 4px;
}

.el-form img {
    border-radius: 4px;
}

.el-form .absolute {
    top: -15px;
    right: -10px;
    z-index: 99;
}
</style>