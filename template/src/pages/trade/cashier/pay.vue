<template>
    <myhead :exclude="['category']"></myhead>
    <div class="main bgf">
        <div class="w bgf pt10 ">
            <el-steps v-if="orderInfo.payType" class="mt10" :active="orderInfo.payType == 'COD' ? 4 : 2"
                finish-status="success" process-status="process" simple>
                <el-step title="选择商品" />
                <el-step title="确认订单" />
                <el-step v-if="orderInfo.payType != 'COD'" title="付款" />
                <el-step title="商家发货" />
                <el-step title="确认收货" />
                <el-step v-if="orderInfo.payType == 'COD'" title="付款" />
                <el-step title="评价" />
            </el-steps>

            <div v-loading="loading" class="payment mt20">
                <div v-if="Object.values(payments).length > 0">
                    <div class="f-gray mb10 flex-middle"><el-icon>
                            <Bell />
                        </el-icon><span class="f-13 ml5">支付款进入平台账户，由平台提供资金保全服务</span>
                    </div>
                    <div class="uni-flex uni-row hd pd10 vertical-middle mb10 bgp">
                        <p class="uni-flex uni-column width-surplus pt5 pb5">
                            <label class="f-gray">商品信息：</label>
                            <span v-if="orderInfo.title" class="line-clamp-1 f-13 mt10">{{ orderInfo.title }}</span>
                        </p>
                        <p class="pt5 pb5">
                            <label class="f-gray">付款金额：</label>
                            <span class="f-20 bold f-red f-yahei">{{ currency(orderInfo.amount) }}</span>
                        </p>
                    </div>

                    <div class="bd pt10 pb10 mb10">
                        <div v-for="(payment) in payments"
                            :class="['uni-flex uni-row flex-middle pd10 item', payment.code, payment.code == form.payment_code ? 'selected' : '', payment.disabled ? 'disabled' : 'pointer']"
                            @click="() => { if (!payment.disabled) { form.payment_code = payment.code } }">
                            <p class="f-14 mr10 width-surplus">
                                <i v-if="payment.code == 'wxpay'" class="iconfont icon-weixinzhifu1 mr10 f-20 f-green"></i>
                                <i v-else-if="payment.code == 'alipay'" class="iconfont icon-zhifubao mr10 f-20 f-blue"></i>
                                <i v-else-if="payment.code == 'cod'"
                                    class="iconfont icon-huodaofukuan mr10 f-20 f-green"></i>
                                <i v-else class="iconfont icon-yue mr10 f-20 f-c60"></i>
                                <span>{{ payment.name }}</span>
                                <label v-if="payment.disabled_desc" class="ml10 f-13 f-gray">
                                    （{{ payment.disabled_desc }}）
                                </label>
                                <label v-else-if="payment.code == 'deposit'" class="ml10 f-13 f-gray">
                                    （可用余额：{{ currency(deposit.money) }} 元）
                                </label>
                            </p>
                            <el-icon :class="['icon f-20', payment.code == form.payment_code ? 'f-green' : 'f-gray']">
                                <CircleCheckFilled />
                            </el-icon>
                        </div>
                    </div>
                    <div class="ml20 pb20 mt20">
                        <div v-if="form.payment_code == 'deposit'" class="uni-flex uni-row mb20 flex-middle">
                            <span style="width:80px;">支付密码：</span>
                            <el-input v-model="form.password" type="password" style="width:120px;" clearable />
                            <label class="f-gray f-13 ml10">忘记密码 ? <router-link to="/deposit/setting/pay"
                                    class="rlink f-gray">前去设置</router-link></label>
                        </div>
                        <el-button @click="submit" type="primary" size="large" class="mb20">确认付款</el-button>
                    </div>
                </div>
                <div v-if="!loading && Object.values(payments).length == 0" class="pd10 mt20 mb20 center bpg round-edge">
                    <el-empty description="暂无可用的支付方式" />
                </div>
            </div>
        </div>
    </div>
    <el-dialog v-model="dialogVisible" title="微信支付" :width="280" :draggable="true" :close-on-click-modal="false" center>
        <div class="center">
            <img :src="form.qrcode" width="200" height="200" />
            <p>请使用微信扫描二维码支付</p>
        </div>
    </el-dialog>
    <myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { currency, redirect } from '@/common/util.js'
import { cashierBuild, cashierPay, cashierCheckpay } from '@/api/cashier.js'
import { depositRead } from '@/api/deposit.js'

import myhead from '@/pages/layout/header/home.vue'
import myfoot from '@/pages/layout/footer/home.vue'

const route = useRoute()
const loading = ref(true)
const orderInfo = reactive({})
const payments = reactive({})
const deposit = ref({})
const form = reactive({})
const dialogVisible = ref(false)

onMounted(() => {
    cashier()

    depositRead(null, (data) => {
        deposit.value = data
    })
})

/**
 * 获取收银台数据
 */
function cashier() {
    cashierBuild(route.params, (data) => {
        Object.assign(orderInfo, data.orderInfo)
        Object.assign(payments, data.payments)
        form.orderId = data.orderId

        // 绑定默认支付方式
        defpayment()
    }, loading)
}

/**
 * 绑定默认支付方式
 */
function defpayment() {
    for (let index in payments) {
        if (payments[index].selected && !payments[index].disabled) {
            form.payment_code = index
            break
        }
        if (!payments[index].disabled && !form.payment_code) {
            form.payment_code = index
        }
    }
}

/**
 * 提交订单
 */
function submit() {
    cashierPay(form, (data) => {
        if (data) {
            if (form.payment_code == 'wxpay') {
                dialogVisible.value = true
                form.qrcode = data.qrcode

                let timer = setInterval(() => {
                    cashierCheckpay(data, (result) => {
                        if (result.ispay) {
                            clearInterval(timer)
                            redirect('/deposit/trade/detail/' + result.tradeNo)
                        }
                    })
                }, 1000)
            } else if (data.redirect) {
                location.href = data.redirect
            } else {
                redirect('/trade/cashier/result/' + data.payTradeNo)
            }
        }
    })
}
</script>

<style scoped>
.payment .hd {
    border: 3px #A0BDDF solid;
    border-radius: 3px;
}

.payment .item {
    padding: 20px 20px;
    border-bottom: 1px #e7f0f1 solid;
}

.payment .item.selected {
    background-color: #e7f0f1;
    border-radius: 4px;
}

.payment .item.wxpay .iconfont {
    color: #04BE02;
}

.payment .item.alipay .iconfont {
    color: #1677ff;
}

.payment .item.deposit .iconfont {
    color: #F0AD4E;
}

.payment .item.disabled,
.payment .item.disabled .icon,
.payment .item.disabled label,
.payment .item.disabled .iconfont {
    color: #cccccc;
}
</style>