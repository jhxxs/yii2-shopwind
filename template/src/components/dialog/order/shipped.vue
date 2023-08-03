<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="500" :center="true" :draggable="true" :destroy-on-close="true"
        :close-on-click-modal="false" :before-close="close">
        <el-form>
            <el-form-item label="物流公司">
                <el-select v-model="form.express_comkey" placeholder="请选择">
                    <el-option v-for="(item, index) in companys" :key="index" :label="item.name" :value="item.code" />
                </el-select>
            </el-form-item>
            <el-form-item v-if="form.express_comkey == 'noneed'" label="发货说明">
                <el-input v-model="form.content" type="textarea" resize="none" placeholder="如虚拟商品提供的服务" />
            </el-form-item>
            <el-form-item v-else label="物流单号">
                <el-input v-model="form.express_no" :clearable="true" />
            </el-form-item>
        </el-form>
        <template #footer>
            <el-button @click="close">关闭</el-button>
            <el-button type="primary" @click="submit" :loading="loading">提交</el-button>
        </template>
    </el-dialog>
</template>
<script setup>
/**
 * 本组件兼容实物发货和虚拟发货
 */
import { onMounted, ref, reactive, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { orderUpdate } from '@/api/order.js'
import { companyList } from '@/api/delivery.js'
import { webimSend } from '@/api/webim.js'

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
const form = reactive({ express_comkey: 'noneed' })
const companys = ref([])

onMounted(() => {
    companyList(null, (data) => {
        companys.value = data.list
    })
})

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})
watch(() => props.data, (value) => {
    // noneed = 针对虚拟商品发货
    form.express_comkey = value.gtype == 'material' ? value.express_comkey : 'noneed'
    form.express_no = value.express_no
})

const emit = defineEmits(['close'])
const submit = () => {
    orderUpdate({ order_id: props.data.order_id, express_comkey: form.express_comkey, express_no: form.express_no, status: 30 }, (data) => {
        // 针对虚拟商品订单（无需发货）对买家发送一条消息
        if (form.express_comkey == 'noneed') {
            webimSend(Object.assign(form, { toid: props.data.buyer_id, store_id: parseInt(props.data.seller_id) }))
        }
        ElMessage.success(props.data.status == data.status ? '物流信息已修改' : '发货成功')
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

.el-form .el-select {
    width: 240px;
}
</style>