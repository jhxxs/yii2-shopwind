<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="600" :center="true" :draggable="true" :destroy-on-close="true"
        :close-on-click-modal="false" :before-close="close">
        <el-form :inline="true">
            <el-form-item label="收货人姓名" :label-width="85">
                <el-input v-model="address.consignee" />
            </el-form-item>
            <el-form-item label="所在地区" :label-width="85" class="uni-flex uni-row" style="width:100%">
                <multiselector api="region/list" idField="region_id" nameField="name" parentField="parent_id"
                    :original="[address.province, address.city, address.district]" @callback="callback">
                </multiselector>
            </el-form-item>
            <el-form-item label="详细地址" :label-width="85">
                <el-input v-model="address.address" />
            </el-form-item>
            <el-form-item label="手机号码" :label-width="85">
                <el-input v-model="address.phone_mob" />
            </el-form-item>
            <el-form-item label="固话号码" :label-width="85">
                <el-input v-model="address.phone_tel" />
            </el-form-item>
            <el-form-item label="设为默认" :label-width="85" style="width:100%">
                <el-switch v-model="address.defaddr" active-value="1" inactive-value="0" />
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
import { addressAdd, addressUpdate } from '@/api/address.js'
import multiselector from '@/components/selector/multiselector.vue'

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
const address = ref({})

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})
watch(() => props.data, (value) => {
    address.value = value
})

const emit = defineEmits(['close'])
const submit = () => {
    if (address.value.addr_id) {
        addressUpdate(address.value, (data) => {
            ElMessage.success('编辑成功')
            emit('close', data, 'update')
        }, loading)
    } else {
        addressAdd(address.value, (data) => {
            ElMessage.success('添加成功')
            emit('close', data)
        }, loading)
    }
}
const close = () => {
    emit('close', null)
}

const callback = (value) => {
    address.value.region_id = value.id

    value.label.forEach((item, index) => {
        address.value[index == 0 ? 'province' : (index == 1 ? 'city' : (index == 2 ? 'district' : 'town'))] = item
    })
}
</script>
<style scoped>
.el-form {
    margin: 0 30px;
}

:deep() .el-select {
    margin-bottom: 10px;
}
</style>