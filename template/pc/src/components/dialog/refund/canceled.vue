<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="400" :center="true" :draggable="true"
        :destroy-on-close="true" :close-on-click-modal="false" :before-close="close">
        <div class="center">您确定要取消该订单（订单号：{{ props.data.order_sn }}）的退款申请吗？</div>
        <template #footer>
            <el-button @click="close">关闭</el-button>
            <el-button type="primary" @click="submit" :loading="loading">提交</el-button>
        </template>
    </el-dialog>
</template>
<script setup>
import { ref, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { refundCancel } from '@/api/refund.js'

const props = defineProps({
    title: { type: String, default: '' },
    visible: { type: Boolean, default: false },
    data: {
        type: [Object, Array],
        default: () => {
            return {}
        }
    }
})

const dialogVisible = ref(false)
const loading = ref(false)

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})

const emit = defineEmits(['close'])
const submit = () => {
    refundCancel({ refund_id: props.data.refund_id }, (data) => {
        ElMessage.success('退款已取消')
        emit('close', { status: 'CLOSED' })
    }, loading)
}
const close = () => {
    emit('close', null)
}

</script>