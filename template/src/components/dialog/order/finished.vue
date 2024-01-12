<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="500" :center="true" :draggable="true" :destroy-on-close="true"
        :close-on-click-modal="false" :before-close="close">
        <div class="center mb20" v-if="props.data.payment_code == 'cod'">
            该订单为货到付款，确认收货后，您需要向商家支付货款<span class="f-red">{{ currency(props.data.order_amount)
            }}</span>
        </div>
        <div class="center mb20" v-else>
            确认收货后交易即完成，货款将打给卖家，您要继续吗？
        </div>
        <template #footer>
            <el-button @click="close">关闭</el-button>
            <el-button type="primary" @click="submit" :loading="loading">确定</el-button>
        </template>
    </el-dialog>
</template>
<script setup>
import { ref, reactive, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { orderUpdate } from '@/api/order.js'
import { currency } from '@/common/util.js'

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
    form.status = props.data.payment_code == 'cod' ? 11 : 40
    orderUpdate({ order_id: props.data.order_id, status: form.status }, (data) => {
        if (props.data.payment_code != 'cod') {
            ElMessage.success('您已经确认收货，交易完成！')
        }

        emit('close', data, props.data.payment_code == 'cod')
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