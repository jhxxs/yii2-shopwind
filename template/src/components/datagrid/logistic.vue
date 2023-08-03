<template>
    <div class="pd10" v-loading="loading">
        <el-row class="pl10 pr10 pt10">
            <el-col :span="6">快递公司：{{ logistic.company }}</el-col>
            <el-col :span="18">物流单号：{{ logistic.number }}</el-col>
        </el-row>
        <el-row class="pl10 pr10">
            <el-timeline v-if="logistic.details && logistic.details.length > 0" class="mt20 pt10">
                <el-timeline-item v-for="(item, index) in logistic.details" :key="index" :timestamp="item.time"
                    :color="index == 0 ? '#0bbd87' : ''">
                    <el-card v-if="index == 0"> {{ item.context }}</el-card>
                    <span v-else> {{ item.context }}</span>
                </el-timeline-item>
            </el-timeline>
            <el-col v-else>
                <el-divider></el-divider>
                暂时没有物流信息
                <p v-if="logistic.url && logistic.url.message" class="f-gray f-13 mt5">{{ logistic.url.message }}</p>
            </el-col>
        </el-row>
    </div>
</template>
<script setup>
import { ref, watch } from 'vue'
import { orderLogistic } from '@/api/order.js'

const props = defineProps({
    data: {
        type: Object,
        default: () => {
            return {}
        }
    }
})

const loading = ref(false)
const logistic = ref({})

orderLogistic({ order_id: props.data.id }, (data) => {
    logistic.value = data
}, loading)


</script>