<template>
    <div v-loading="loading">
        <el-timeline class="pd10 mt20">
            <el-timeline-item v-for="(item, index) in gallery" :color="index == 0 ? '#0bbd87' : ''" :timestamp="item.time">
                <span :class="index == 0 ? 'bold' : ''">{{ item.label }}</span>
            </el-timeline-item>
        </el-timeline>
    </div>
</template>
<script setup>
import { ref, watch } from 'vue'
import { orderTimeline } from '@/api/order.js';

const props = defineProps({
    data: {
        type: Object,
        default: () => {
            return {}
        }
    }
})

const loading = ref(true)
const gallery = ref([])

watch(() => props.data, (value) => {
    orderTimeline({ order_id: value.order_id }, (data) => {
        gallery.value = data
    }, loading)
})

</script>

