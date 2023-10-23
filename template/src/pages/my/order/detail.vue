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
                                <el-breadcrumb-item>订单详情</el-breadcrumb-item>
                            </el-breadcrumb>
                        </div>
                    </div>
                    <div class="round-edge pd10 bgf mt20">
                        <ordertimeline :data="order"></ordertimeline>
                    </div>
                    <div class="round-edge pd10 bgf mt20">
                        <div class="pl10 pr10 pt10">
                            <h3 class="f-14">订单信息</h3>
                            <el-row class="f-13 f-c55 mt20 mb10">
                                <el-col :span="12">订单编号：{{ order.order_sn }}</el-col>
                                <el-col :span="12">订单总价：<span class="f-red f-yahei">{{
                                    currency(order.order_amount)
                                }}</span></el-col>
                                <el-col :span="12">卖家店铺：{{ order.seller_name }}</el-col>
                                <el-col :span="12">配送费用：<span class="f-red f-yahei">{{
                                    currency(order.shipping_fee)
                                }}</span></el-col>
                                <el-col :span="12">支付方式：{{ order.payment_name }}</el-col>
                                <el-col :span="12">交易编号：{{ order.tradeNo || '-' }}</el-col>
                            </el-row>
                        </div>
                    </div>
                    <div v-if="shipping" class="round-edge pd10 bgf mt20">
                        <div class="pl10 pr10 pt10">
                            <h3 class="f-14">配送信息</h3>
                            <el-row class="f-13 f-c55 mt20 mb10">
                                <el-col :span="12">收货人姓名：{{ order.buyer_name }}</el-col>
                                <el-col :span="12">收货地址：{{ shipping.province + shipping.city + (shipping.district ||
                                    '') + shipping.address
                                }}</el-col>
                                <el-col :span="12">联系电话：{{ shipping.phone_mob }}</el-col>
                                <el-col :span="12">发货时间：{{ order.ship_time || '-' }}</el-col>
                                <el-col :span="12" v-if="order.express">配送方式：{{ order.express.company || '-' }}</el-col>
                                <el-col :span="12" v-if="order.express">物流单号：{{ order.express.number || '-' }}</el-col>
                            </el-row>
                        </div>
                    </div>
                    <div v-if="order.qrcode" class="round-edge pd10 bgf mt20">
                        <div class="pl10 pr10 pt10">
                            <h3 class="f-14">核销码</h3>
                            <el-row class="f-13 f-c55 mt20 mb10 qrcode">
                                <el-col>
                                    <img width="158" height="158" class="pd5 block img" :src="order.qrcode" />
                                    <p>将二维码出示给商家完成核销</p>
                                </el-col>
                            </el-row>
                        </div>
                    </div>
                    <div class="round-edge pd10 bgf mt20">
                        <div class="pl10 pr10 pt10">
                            <h3 class="f-14">商品信息</h3>
                            <el-row class="f-13 f-c55 mt20">
                                <el-col :span="12" v-for="item in order.items" class="mb10">
                                    <el-col :span="4">
                                        <router-link :to="'/goods/detail/' + item.goods_id" class="rlink mr10">
                                            <img :src="item.goods_image" width="60" height="60"></router-link>
                                    </el-col>
                                    <el-col :span="20" class="l-h20">
                                        <router-link :to="'/goods/detail/' + item.goods_id" class="rlink line-clamp-2">{{
                                            item.goods_name }}</router-link>
                                        <p v-if="item.specification" class="f-gray">{{ item.specification }}</p>
                                        <p class="mt5 f-red">{{ currency(item.price) }} x {{ item.quantity }}</p>
                                    </el-col>
                                </el-col>
                            </el-row>
                        </div>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
    <myfoot></myfoot>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { orderRead, orderExtm, orderGoods, orderQrcode } from '@/api/order.js'
import { currency } from '@/common/util.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'
import ordertimeline from '@/components/datagrid/ordertimeline.vue'

const route = useRoute()
const loading = ref(false)
const order = ref({items:[]})
const shipping = ref()

onMounted(() => {
    orderRead({ order_id: route.params.id }, (data) => {
        order.value = data
        orderGoods({ order_id: data.order_id }, (items) => {
            order.value.items = items.list
        })

        if (data.gtype == 'service' && data.pay_time) {
            orderQrcode({ order_id: route.params.id }, (res) => {
                Object.assign(order.value, res)
            })
        }
    }, loading)

    orderExtm({ order_id: route.params.id }, (data) => {
        shipping.value = data
    })
})
</script>

<style scoped>
.wraper .el-row {
    line-height: 30px;
}

.wraper .el-divider {
    margin: 16px 0;
}

.qrcode .img {
    border: 1px #f1f1f1 solid;
}
</style>
