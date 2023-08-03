<template>
    <myhead></myhead>
    <div class="main w pt10">
        <el-row :gutter="12">
            <el-col :span="4">
                <menus></menus>
            </el-col>
            <el-col :span="20">
                <div class="wraper" v-loading="loading">
                    <div class="round-edge pd10 bgf">
                        <div class="pd10">
                            <el-breadcrumb separator="/">
                                <el-breadcrumb-item>订单</el-breadcrumb-item>
                                <el-breadcrumb-item>我的订单</el-breadcrumb-item>
                                <el-breadcrumb-item>订单评价</el-breadcrumb-item>
                            </el-breadcrumb>
                        </div>
                    </div>
                    <div class="round-edge pd10 bgf mt20">
                        <div class="pd10">
                            <h3 class="f-14">商品评价</h3>
                            <div class="pd10">
                                <el-row v-for="item in order.items" class="f-13 f-c55 mt20 mb10">
                                    <el-col :span="3"><img :src="item.goods_image" width="60" height="60"
                                            style="margin:4px 10px 0 0"></el-col>
                                    <el-col :span="12" class="l-h20">
                                        <p class="line-clamp-2">{{ item.goods_name }}</p>
                                        <p v-if="item.specification" class="f-gray">{{ item.specification }}</p>
                                        <p class="mt5 f-red">{{ currency(item.price) }} x {{ item.quantity }}</p>
                                        <p class="mt10 mb5">
                                            <el-rate v-model="evaluations.goods[item.spec_id].value" />
                                        </p>
                                        <div class="uni-flex uni-row mb20 mt20 share flex-wrap">
                                            <div v-for="(image, index) in evaluations.images[item.spec_id]" :key="index"
                                                class="relative">
                                                <el-upload v-if="image" action="#" :show-file-list="false"
                                                    :auto-upload="false"
                                                    :on-change="(file) => { fileUpload(file, item.spec_id, index) }"
                                                    class="mr20 mb20">
                                                    <img :src="image + '?t=' + Math.random()" />
                                                    <el-icon @click.stop="remove(item.spec_id, index)"
                                                        class="absolute remove" color="#ffffff">
                                                        <close-bold />
                                                    </el-icon>
                                                </el-upload>
                                            </div>
                                            <el-upload action="#" :show-file-list="false" :auto-upload="false"
                                                :on-change="(file) => { fileUpload(file, item.spec_id, evaluations.images[item.spec_id].length) }"
                                                class="mr20">
                                                <el-icon class="add" :size="20">
                                                    <plus />
                                                </el-icon>
                                            </el-upload>
                                        </div>
                                        <p>
                                            <el-input v-model="evaluations.goods[item.spec_id].comment"
                                                placeholder="输入您对宝贝的评价" />
                                        </p>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                    </div>

                    <div class="round-edge pd10 bgf mt20">
                        <div class="pd10">
                            <h3 class="f-14 mb20">店铺评分</h3>
                            <div class="pd10">
                                <el-row>
                                    <el-col :span="3">服务评分</el-col>
                                    <el-col :span="12">
                                        <el-rate v-model="evaluations.store.service" />
                                    </el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="3">物流评分</el-col>
                                    <el-col :span="12">
                                        <el-rate v-model="evaluations.store.shipped" class="mt5" />
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                        <div v-if="order.evaluation_status == 0" class="pd10 center mb20">
                            <el-button @click="submit" type="primary">提交评价</el-button>
                        </div>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
    <myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
import { orderRead, orderGoods, orderEvaluate } from '@/api/order.js'
import { uploadFile } from '@/api/upload.js'
import { currency } from '@/common/util.js'

import router from '@/router'
import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const route = useRoute()
const loading = ref(false)
const order = ref({})
const evaluations = reactive({ goods: {}, store: { service: 5, shipped: 5 }, images: {} })

onMounted(() => {
    orderRead({ order_id: route.params.id }, (data) => {
        orderGoods({ order_id: data.order_id }, (items) => {
            data.items = items.list
            order.value = data

            data.items.forEach(function (item, index) {
                evaluations.images[item.spec_id] = item.images || []
                evaluations.goods[item.spec_id] = {
                    value: 5,
                    comment: ''
                }
            })
        })
    }, loading)
})

const submit = () => {
    orderEvaluate({ order_id: order.value.order_id, evaluations: evaluations }, () => {
        order.value.evaluation_status = 1
        ElMessage.success('您已完成评价！')
        router.replace('/my/order/detail/' + order.value.order_id)
    })
}

const remove = (id, index) => {
    evaluations.images[id].splice(index, 1)
}

const fileUpload = (file, id, index) => {
    uploadFile(file.raw, { folder: 'evaluate/' }, (data) => {
        evaluations.images[id][index] = data.fileUrl
    })
}
</script>

<style scoped>
.wraper .el-row {
    line-height: 30px;
}

.wraper .el-rate {
    margin-top: 8px
}

.wraper .el-divider {
    margin: 16px 0;
}

.share img,
.share .add {
    border: 1px #ddd dotted;
    width: 100px;
    height: 100px;
    line-height: 100px;
    border-radius: 4px;
}

.share .remove {
    background-color: #000;
    opacity: 0.7;
    right: 10px;
    top: -10px;
    border-radius: 100%;
    padding: 3px;
}
</style>
