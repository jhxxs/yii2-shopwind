<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="600" :center="true" :draggable="true"
        :destroy-on-close="true" :close-on-click-modal="false" :before-close="close">
        <el-form :inline="true">
            <el-form-item label="优惠券名称" :label-width="85">
                <el-input v-model="coupon.name" clearable />
            </el-form-item>
            <el-form-item label="优惠券数量" :label-width="85">
                <el-input v-model="coupon.total" :disabled="coupon.id" class="number" style="margin-right:300px;"
                    clearable />
            </el-form-item>
            <el-form-item label="优惠金额" :label-width="85">
                <el-input v-model="coupon.money" class="number" clearable />
                <span class="f-13 f-gray ml10">元</span>
            </el-form-item>
            <el-form-item label="购满金额" :label-width="85">
                <el-input v-model="coupon.amount" class="number" clearable />
                <span class="f-13 f-gray ml10">元，单笔订单购满多少金额可用</span>
            </el-form-item>
            <el-form-item label="有效期起" :label-width="85">
                <el-date-picker v-model="coupon.start_time" type="date" format="YYYY-MM-DD" value-format="YYYY-MM-DD" />
            </el-form-item>
            <el-form-item label="有效期至" :label-width="85">
                <el-date-picker v-model="coupon.end_time" type="date" format="YYYY-MM-DD" value-format="YYYY-MM-DD" />
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="mb20">
                <el-button @click="close">关闭</el-button>
                <el-button type="primary" @click="submit" :loading="loading">提交</el-button>
            </div>
        </template>
    </el-dialog>
</template>
<script setup>
import { ref, reactive, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { couponAdd, couponUpdate } from '@/api/coupon.js'

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
const coupon = ref({})

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})
watch(() => props.data, (value) => {
    coupon.value = value
})

const emit = defineEmits(['close'])
const submit = () => {
    if (coupon.value.id) {
        couponUpdate(coupon.value, (data) => {
            ElMessage.success('编辑成功')
            emit('close', coupon.value)
        }, loading)
    } else {
        couponAdd(coupon.value, (data) => {
            ElMessage.success('添加成功')
            emit('close', Object.assign(coupon.value, { available: 1, surplus: coupon.value.total }))
        }, loading)
    }
}
const close = () => {
    emit('close', null)
}

</script>
<style scoped>
.el-form {
    margin: 0 30px;
}

.el-form .el-input.number {
    width: 100px;
}

:deep() .el-date-editor .el-input__inner {
    padding-left: 34px !important;
}
</style>