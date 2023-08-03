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
                                <el-breadcrumb-item>资产</el-breadcrumb-item>
                                <el-breadcrumb-item>交易详情</el-breadcrumb-item>
                            </el-breadcrumb>
                        </div>
                    </div>
                    <div class="round-edge pd10 bgf mt20">
                        <p class="mt5 mb5 pl10 pr10 bold">
                            <span
                                :class="[trade.status == 'SUCCESS' ? 'f-blue' : (trade.status == 'CLOSED' ? 'f-gray' : '')]">{{
                                        translator(trade.status,
                                            'trade')
                                }}</span>
                        </p>
                    </div>
                    <div class="round-edge pd10 bgf mt20">
                        <div class="pl10 pr10 pt10">
                            <h3 class="f-14">交易信息</h3>
                            <el-row class="f-13 f-c55 mt20 mb10">
                                <el-col :span="12">消费名称：{{ truncate(trade.title, 30) }}</el-col>
                                <el-col :span="12">交易编号：{{ trade.tradeNo }}</el-col>
                                <el-col :span="12">交易金额：<span class="f-red f-yahei">{{
                                        currency(trade.amount)
                                }}</span></el-col>
                                <el-col :span="12">支付方式：{{ trade.payment_name }}</el-col>
                                <el-col :span="12">商户交易号：{{ trade.bizOrderId || '-' }}</el-col>

                                <el-col :span="12">创建时间：{{ trade.add_time }}</el-col>
                                <el-col :span="12">付款时间：{{ trade.pay_time || '-' }}</el-col>
                                <el-col :span="12">完成时间：{{ trade.end_time || '-' }}</el-col>
                            </el-row>
                        </div>
                    </div>
                    <div v-if="trade.orderInfo" class="round-edge pd10 bgf mt20">
                        <div class="pl10 pr10 pt10">
                            <h3 class="f-14">商品信息</h3>
                            <el-row class="f-13 f-c55 mt20">
                                <el-col :span="12" v-for="item in trade.orderInfo.items" class="mb10">
                                    <el-col :span="4">
                                        <router-link :to="'/goods/detail/' + item.goods_id" class="rlink mr10">
                                            <img :src="item.goods_image" width="60" height="60">
                                        </router-link>
                                    </el-col>
                                    <el-col :span="20" class="l-h20">
                                        <router-link :to="'/goods/detail/' + item.goods_id" class="rlink line-clamp-2">{{
                                                item.goods_name
                                        }}</router-link>
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
import { depositTrade } from '@/api/deposit.js'
import { currency, translator, truncate } from '@/common/util.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const route = useRoute()
const loading = ref(false)
const trade = ref({})

onMounted(() => {
    depositTrade({ tradeNo: route.params.id }, (data) => {
        trade.value = data
    })
})
</script>

<style scoped>
.wraper .el-row {
    line-height: 30px;
}
</style>
