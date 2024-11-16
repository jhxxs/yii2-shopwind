<template>
    <myhead></myhead>
    <div class="main w pt10">
        <el-row :gutter="12">
            <el-col :span="4">
                <menus></menus>
            </el-col>
            <el-col :span="20">
                <div class="wraper">
                    <div class="round-edge pd10 bgf">
                        <div class="pd10">
                            <el-breadcrumb separator="/">
                                <el-breadcrumb-item>售后</el-breadcrumb-item>
                                <el-breadcrumb-item>我的退款</el-breadcrumb-item>
                                <el-breadcrumb-item v-if="!order.refund_sn">发起退款</el-breadcrumb-item>
                                <el-breadcrumb-item v-else>修改退款</el-breadcrumb-item>
                            </el-breadcrumb>
                        </div>
                    </div>
                    <div class="round-edge pd10 bgf mt20">
                        <el-row style="margin:10px 30px;">
                            <el-col>
                                <h2 v-if="!order.refund_sn" class="mb20 f-18">您的订单({{ order.order_sn }})正在申请退款</h2>
                                <h2 v-else class="mb20 f-18">您正在修改退款信息(退款单号：{{ order.refund_sn }})</h2>
                                <el-form class="pt10">
                                    <el-form-item label="退款金额">
                                        <el-input v-model="form.refund_total_fee"
                                            :placeholder="order.refund_total_fee || order.order_amount" class="w-50 m-2"
                                            :clearable="true" style="width:120px" />
                                        <span class="f-yahei f-gray ml10 f-13">最多退款{{ currency(order.order_amount)
                                        }}元</span>
                                    </el-form-item>
                                    <el-form-item label="退款原因">
                                        <el-select v-model="form.refund_reason" placeholder="请选择">
                                            <el-option v-for="(item, index) in reasonOptions" :key="index" :label="item"
                                                :value="item" />
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item label="收货情况">
                                        <el-select v-model="form.shipped" placeholder="请选择">
                                            <el-option v-for="(item, index) in shippedOptions" :key="index"
                                                :label="item" :value="index" />
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item label="退款说明">
                                        <el-input v-model="form.refund_desc" :rows="2" type="textarea"
                                            placeholder="退货商品瑕疵描述" style="width:300px;" />
                                    </el-form-item>

                                    <el-form-item label=" " :label-width="68">
                                        <el-button type="primary" @click="submit" :loading="loading">提交</el-button>
                                    </el-form-item>
                                </el-form>
                            </el-col>
                        </el-row>

                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
    <myfoot></myfoot>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
import { orderRead } from '@/api/order.js'
import { refundRead, refundCreate, refundUpdate } from '@/api/refund.js'
import { currency } from '@/common/util.js'

import router from '@/router'
import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const route = useRoute()
const loading = ref(false)
const order = reactive({})
const form = reactive({})
const shippedOptions = reactive(['未收到货，需要退款', '已收到货，不退货只退款', '已收到货，需退货退款'])
const reasonOptions = reactive(['缺货', '未按约定时间发货', '商品有质量问题', '商品错发/漏发', '收到的商品描述不符', '其他'])

onMounted(() => {

    orderRead({ order_id: route.params.order_id }, (res) => {
        Object.assign(order, res)
        form.refund_total_fee = res.order_amount
        if (res.refund_sn) {
            refundRead({ refund_sn: res.refund_sn }, (data) => {
                Object.assign(form, data)
            })
        }
    }, loading)
})

const submit = () => {
    form.order_id = route.params.order_id
    loading.value = true

    // 创建退款
    if (!order.refund_sn) {
        refundCreate(form, (data) => {
            ElMessage.success('退款申请提交成功')
            router.replace('/my/refund/detail/' + data.refund_id)
        })
    }
    // 修改退款
    else {
        refundUpdate(form, (data) => {
            ElMessage.success('退款修改成功，等待卖家处理')
            router.replace('/my/refund/detail/' + data.refund_id)
        })
    }
    setTimeout(() => {
        loading.value = false
    }, 1000);
}
</script>
