<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="600" :center="true" :draggable="true"
        :destroy-on-close="true" :close-on-click-modal="false" :before-close="close">
        <el-form>
            <el-form-item label="取消原因">
                <el-select v-model="form.reason" placeholder="请选择">
                    <el-option v-for="(item, index) in options" :key="index" :label="item" :value="item" />
                </el-select>
            </el-form-item>
            <el-form-item label="备注信息">
                <el-input v-model="form.remark" :clearable="true" />
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
import { orderUpdate } from '@/api/order.js'

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
const options = ref(['不想要了', '无法备齐货物', '协商一致同意', '其他原因'])

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})

const emit = defineEmits(['close'])
const submit = () => {
    orderUpdate({ order_id: props.data.order_id, reason: form.reason, remark: form.remark, status: 0 }, (data) => {
        ElMessage.success('订单已取消')
        emit('close', data)
    }, loading)
}
const close = () => {
    emit('close', null)
}

</script>
<style scoped>
.el-form {
    margin: 0 100px;
}
</style>