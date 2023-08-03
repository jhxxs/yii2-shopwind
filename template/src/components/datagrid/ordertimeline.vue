<template>
    <div v-loading="loading">
        <el-timeline v-if="order.payType == 'COD'" class="pd10 mt20">
            <el-timeline-item v-if="order.finished_time" color="#0bbd87" :timestamp="order.finished_time">
                <strong>{{ translator(order.status) }}</strong>
            </el-timeline-item>

            <el-timeline-item v-if="order.pay_time" :timestamp="order.pay_time"
                :color="order.finished_time ? '' : '#0bbd87'">
                买家已付款
            </el-timeline-item>
            <el-timeline-item v-else-if="order.receive_time" color="#0bbd87">
                待支付货款
            </el-timeline-item>

            <el-timeline-item v-if="order.receive_time" :timestamp="order.receive_time">
                买家已收货
            </el-timeline-item>
            <el-timeline-item v-if="order.ship_time" :timestamp="order.ship_time"
                :color="order.receive_time ? '' : '#0bbd87'">
                {{ translator(30) }}
            </el-timeline-item>
            <el-timeline-item v-else :color="(order.ship_time || order.finished_time) ? '' : '#0bbd87'">
                {{ translator(20) }}
            </el-timeline-item>
            <el-timeline-item :timestamp="order.add_time">提交订单</el-timeline-item>
        </el-timeline>
        <el-timeline v-else class="pd10 mt20">
            <el-timeline-item v-if="order.finished_time" color="#0bbd87" :timestamp="order.finished_time">
                <strong>{{ translator(order.status) }}</strong>
            </el-timeline-item>
            <el-timeline-item v-if="order.receive_time" :timestamp="order.receive_time"
                :color="order.finished_time ? '' : '#0bbd87'">
                买家已收货
            </el-timeline-item>
            <el-timeline-item v-if="order.ship_time" :timestamp="order.ship_time"
                :color="order.receive_time ? '' : '#0bbd87'">
                {{ translator(30) }}
            </el-timeline-item>
            <el-timeline-item v-if="order.pay_time" :timestamp="order.pay_time" :color="order.ship_time ? '' : '#0bbd87'">
                {{ translator(20) }}
            </el-timeline-item>
            <el-timeline-item :timestamp="order.add_time"
                :color="(order.pay_time || order.finished_time) ? '' : '#0bbd87'">提交订单</el-timeline-item>
        </el-timeline>
    </div>
</template>
<script setup>
import { ref, watch } from 'vue'
import { translator } from '@/common/util.js'

const props = defineProps({
    data: {
        type: Object,
        default: () => {
            return {}
        }
    }
})

const loading = ref(true)
const order = ref({})
watch(() => props.data, (value) => {
    order.value = value
    loading.value = false
})

</script>

