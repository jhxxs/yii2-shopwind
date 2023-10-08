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
                                <el-breadcrumb-item>售后</el-breadcrumb-item>
                                <el-breadcrumb-item>我的退款</el-breadcrumb-item>
                                <el-breadcrumb-item>退款详情</el-breadcrumb-item>
                            </el-breadcrumb>
                        </div>
                    </div>
                    <div v-if="refund.refund_id">
                        <div class="round-edge pd10 bgf mt20">
                            <h3 class="pd10 f-15">退款原因：{{ refund.refund_reason || '-' }}</h3>
                            <el-divider></el-divider>
                            <el-timeline class="pd10 mt20">
                                <el-timeline-item color="#0bbd87"
                                    :timestamp="refund.finished || getMoment().format('YYYY-MM-DD HH:mm:ss')">
                                    <strong>{{ translator(refund.status, 'refund') }}</strong>
                                </el-timeline-item>
                                <el-timeline-item :timestamp="refund.created">发起退款</el-timeline-item>
                            </el-timeline>
                            <div v-if="refund.status != 'SUCCESS' && refund.status != 'CLOSED'" class="mt20 pl10 pr10 mb20">
                                <el-button @click="cancelClick">取消退款</el-button>
                                <el-button type="primary" @click="modifyClick">修改退款</el-button>
                                <el-button type="success"
                                    @click="redirect('/webim/chat/' + order.seller_id + '/' + order.seller_id)" plain><el-icon
                                        class="mr5">
                                        <Service />
                                    </el-icon>联系客服</el-button>
                            </div>
                        </div>
                        <div class="round-edge pd10 bgf mt20 gallery">
                            <div class="pl10 pr10 pb10">
                                <h3 class="f-14 pt10 pb10">留言信息</h3>
                                <el-scrollbar height="100px">
                                    <div v-for="item in gallery.list" class="f-13 mt10 mb10">
                                        <p class="bold mb5 vertical-middle">
                                            <span class="divider mr5"></span>
                                            {{ item.sender }}
                                        </p>
                                        <p class="f-c55 l-h20">
                                            <span class="mr10">{{ item.content }}</span>
                                            <img v-if="item.image" :src="item.image" class="block" />
                                            <span class="f-gray">[{{ item.created }}]</span>
                                        </p>
                                    </div>
                                </el-scrollbar>
                            </div>
                        </div>
                        <div class="round-edge pd10 bgf mt20">
                            <div class="pl10 pr10 pt10">
                                <h3 class="f-14">订单信息</h3>
                                <el-row class="f-13 f-c55 mt20 mb10">
                                    <el-col :span="12">订单编号：{{ order.order_sn }}</el-col>
                                    <el-col :span="12">退款编号：{{ refund.refund_sn }}</el-col>
                                    <el-col :span="12">卖家店铺：{{ order.seller_name }}</el-col>
                                    <el-col :span="12">订单总价：<span class="f-red f-yahei">{{
                                        currency(order.order_amount)
                                    }}</span></el-col>

                                    <el-col :span="12">支付方式：{{ order.payment_name }}</el-col>
                                    <el-col :span="12">配送费用：<span class="f-red f-yahei">{{
                                        currency(order.shipping_fee)
                                    }}</span></el-col>
                                    <el-col :span="12">下单时间：{{ order.add_time }}</el-col>
                                    <el-col :span="12">支付时间：{{ order.pay_time || '-' }}</el-col>
                                    <el-col :span="12">发货时间：{{ order.ship_time || '-' }}</el-col>
                                    <el-col :span="12">交易完成：{{ order.finished_time || '-' }}</el-col>
                                    <el-col :span="12">退款原因：{{ refund.refund_reason || '-' }}</el-col>
                                    <el-col :span="12">退款说明：{{ refund.refund_desc || '-' }}</el-col>
                                </el-row>
                            </div>
                        </div>
                        <div v-if="order.express" class="round-edge pd10 bgf mt20">
                            <div class="pl10 pr10 pt10">
                                <h3 class="f-14">发货状态</h3>
                                <el-row class="f-13 f-c55 mt20 mb10">
                                    <el-col :span="12">配送方式：{{ order.express.company || '-' }}</el-col>
                                    <el-col :span="12">物流单号：{{ order.express.number || '-' }}</el-col>
                                    <el-col :span="12">发货时间：{{ order.ship_time || '-' }}</el-col>
                                </el-row>
                            </div>
                        </div>
                        <div class="round-edge pd10 bgf mt20">
                            <div class="pl10 pr10 pt10">
                                <h3 class="f-14">商品信息</h3>
                                <el-row class="f-13 f-c55 mt20">
                                    <el-col :span="12" v-for="item in order.items" class="mb10">
                                        <el-col :span="4"><img :src="item.goods_image" width="60" height="60"
                                                style="margin:4px 10px 0 0"></el-col>
                                        <el-col :span="20" class="l-h20">
                                            <p class="line-clamp-2">{{ item.goods_name }}</p>
                                            <p v-if="item.specification" class="f-gray">{{ item.specification }}</p>
                                            <p class="mt5 f-red">{{ currency(item.price) }} x {{ item.quantity }}</p>
                                        </el-col>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                    </div>
                    <div v-else class="round-edge pd10 bgf mt20">
                        <el-empty description="退款已关闭或退款不存在" />
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>

    <canceled title="取消退款" :visible="dialogVisible" :data="refund" @close="dialogClose" />

    <myfoot></myfoot>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useRoute } from 'vue-router'
import { orderRead, orderGoods } from '@/api/order.js'
import { refundRead, refundLogs } from '@/api/refund.js'
import { currency, translator, redirect } from '@/common/util.js'
import { getMoment } from '@/common/moment.js'

import router from '@/router'
import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'
import canceled from '@/components/dialog/refund/canceled.vue'

const route = useRoute()
const loading = ref(false)
const dialogVisible = ref(false)
const refund = ref({})
const order = reactive({})
const gallery = reactive({ list: [] })

onMounted(() => {
    refundRead({ refund_id: route.params.id }, (res) => {
        refund.value = res
        orderRead({ order_id: res.order_id }, (data) => {
            Object.assign(order, data)
            orderGoods({ order_id: data.order_id }, (items) => {
                Object.assign(order, { items: items.list })
            })
        })

        refundLogs({ refund_id: res.refund_id, page_size: 50 }, (data) => {
            gallery.list = data.list
        })

    }, loading)
})
const cancelClick = () => {
    dialogVisible.value = true
}
const modifyClick = () => {
    router.push('/my/refund/build/' + order.order_id)
}
const dialogClose = (value) => {
    dialogVisible.value = false
    Object.assign(refund.value, value ? value : {})
}
</script>

<style scoped>
.wraper .el-row {
    line-height: 30px;
}

.wraper .el-divider {
    margin: 12px 0;
}

.gallery .divider {
    height: 12px;
    width: 3px;
    background: #5b48a0;
    display: inline-block;
    border-radius: 4px;
}</style>
