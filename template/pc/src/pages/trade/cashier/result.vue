<template>
    <myhead :exclude="['category', 'imagead']"></myhead>
    <div class="main w pt10">
        <div class="round-edge pd10 bgf mt10 mb20 center">
            <div class="mt20 mb20 pd10">
                <div v-if="result">
                    <div v-if="result.ispay">
                        <el-icon :size="60" class="f-green mb10">
                            <SuccessFilled />
                        </el-icon>
                        <h3 class="mt10">订单支付成功</h3>
                        <p v-if="result.status == 'ACCEPTED'" class="mt20 f-gray">卖家正在给您安排发货，请耐心等待</p>
                    </div>
                    <!--货到付款-->
                    <div v-else-if="result.payType == 'COD'">
                        <el-icon :size="60" class="f-green mb10">
                            <SuccessFilled />
                        </el-icon>
                        <h3 class="mt10">订单已提交</h3>
                        <p v-if="result.status == 'ACCEPTED'" class="mt20 f-gray">请在收到货后向商家付款，目前订单状态为：等待商家发货</p>
                    </div>
                    <div v-else>
                        <el-icon :size="60" class="f-yellow mb10">
                            <CircleCloseFilled />
                        </el-icon>
                        <h3 class="mt10">支付出现异常</h3>
                        <p class="mt20 f-gray">请核实订单是否已经支付</p>
                    </div>
                </div>
                <div v-else>
                    <el-icon :size="60" class="f-blue mb10">
                        <Clock />
                    </el-icon>
                    <h3 class="mt10">订单处理中，请稍等...</h3>
                </div>
            </div>
        </div>
    </div>
    <myfoot></myfoot>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRoute } from 'vue-router'
import { redirect } from '@/common/util.js'
import { cashierCheckpay } from '@/api/cashier.js'

import myhead from '@/pages/layout/header/home.vue'
import myfoot from '@/pages/layout/footer/home.vue'

const route = useRoute()
const result = ref(null)

setTimeout(() => {
    checkpay()
}, 4000);

function checkpay() {
    cashierCheckpay({ tradeNo: route.params.tradeNo, payTradeNo: route.params.payTradeNo }, (data) => {
        result.value = data

        setTimeout(() => {
            redirect(data.bizIdentity == 'ORDER' ? '/my/order/list' : '/deposit/trade/list')
        }, 2000);

    })
}
</script>

