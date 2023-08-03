<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="600" :center="true" :draggable="true"
        :destroy-on-close="true" :close-on-click-modal="false" :before-close="close">
        <el-form>
            <el-form-item label="充值卡号">
                <el-input v-model="form.cardNo" :clearable="true" />
            </el-form-item>
            <el-form-item label="卡号密码">
                <el-input v-model="form.password" type="password" :clearable="true" />
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
import { cashcardBind } from '@/api/cashcard.js'

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
const form = reactive({ cardNo: '', password: '' })

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})

const emit = defineEmits(['close'])
const submit = () => {
    cashcardBind(form, () => {
        ElMessage.success('充值成功')
        emit('close', true)
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