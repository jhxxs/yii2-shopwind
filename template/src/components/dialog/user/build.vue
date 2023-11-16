<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="400" :center="true" :draggable="true" :destroy-on-close="true"
        :close-on-click-modal="false" :before-close="close">
        <el-form :inline="true">
            <el-form-item label="昵称" :label-width="85">
                <el-input v-model="user.nickname" clearable/>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="mb10">
                <el-button type="primary" @click="submit" :loading="loading">确定</el-button>
            </div>
        </template>
    </el-dialog>
</template>
<script setup>
import { ref, reactive, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { userUpdate } from '@/api/user.js'

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
const user = ref({})

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})
watch(() => props.data, (value) => {
    user.value = value
})

const emit = defineEmits(['close'])
const submit = () => {
    userUpdate({ nickname: user.value.nickname }, (data) => {
        if (data) {
            ElMessage.success('编辑成功')
            emit('close', user.value)
        }
    }, loading)
}

const close = () => {
    emit('close', null)
}

</script>
<style scoped>
.el-form {
    margin: 0 30px;
}

.el-form .el-form-item {
    margin-bottom: 0;
}
</style>