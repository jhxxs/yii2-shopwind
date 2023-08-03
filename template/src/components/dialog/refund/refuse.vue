<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="400" :center="true" :draggable="true"
        :destroy-on-close="true" :close-on-click-modal="false" :before-close="close">
        <el-form>
            <el-form-item label="拒绝理由">
                <el-input v-model="form.content" :rows="2" type="textarea" placeholder="请说明拒绝原因" />
            </el-form-item>
        </el-form>
        <template #footer>
            <el-button @click="close">关闭</el-button>
            <el-button type="primary" @click="submit" :loading="loading">提交</el-button>
        </template>
    </el-dialog>
</template>
<script setup>
import { ref, reactive, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { refundRefuse } from '@/api/refund.js'

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
const form = reactive({})

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})

const emit = defineEmits(['close'])
const submit = () => {
    refundRefuse({ refund_id: props.data.refund_id, content: form.content }, (data) => {
        ElMessage.success('退款已拒绝')
        emit('close', Object.assign(form, data))
    }, loading)
}
const close = () => {
    emit('close', null)
}

</script>