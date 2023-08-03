<template>
    <myhead></myhead>
    <div class="main w pt10">
        <el-row :gutter="12">
            <el-col :span="4">
                <menus></menus>
            </el-col>
            <el-col :span="20">
                <div class="round-edge pd10 bgf">
                    <div class="pd10">
                        <el-breadcrumb separator="/">
                            <el-breadcrumb-item>物流</el-breadcrumb-item>
                            <el-breadcrumb-item>配送时效设置</el-breadcrumb-item>
                        </el-breadcrumb>
                    </div>
                </div>
                <div class="round-edge pd10 bgf mt20">
                    <el-form :inline="true" class="pd10">
                        <div v-for="(item, index) in gallery">
                            <p class="w-full mb20 mt20"><strong class="ml20">时段{{ index > 0 ? '二' : '一' }}：</strong></p>
                            <el-form-item label="下单时间" :label-width="100">
                                <el-time-picker v-model="item.start" format="HH:mm" value-format="HH:mm" arrow-control />
                                <span class="ml10 mr10">至</span>
                                <el-time-picker v-model="item.end" format="HH:mm" value-format="HH:mm" arrow-control />
                            </el-form-item>
                            <el-form-item label="到达时间" :label-width="100">
                                <el-input v-model="item.day" placeholder="3">
                                    <template #append>天</template>
                                </el-input>
                                <span class="ml10 mr10">后</span>
                                <el-time-picker v-model="item.time" format="HH:mm" value-format="HH:mm" arrow-control />
                            </el-form-item>
                        </div>
                        <el-form-item label=" " :label-width="100" class="mt20">
                            <el-button type="primary" @click="submit" :loading="loading">提交</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </el-col>
        </el-row>
    </div>

    <myfoot></myfoot>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ElNotification } from 'element-plus'
import { deliveryTimer, deliveryTimerupdate } from '@/api/delivery.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const loading = ref(false)
const gallery = reactive([{ start: '00:00', end: '16:59', day: 2, time: '18:00' }, { start: '17:00', end: '23:59', day: 3, time: '18:00' }])
const form = reactive({})

onMounted(() => {
    deliveryTimer(null, (data) => {
        Object.assign(gallery, data.rules || [])
    })
})

const submit = () => {
    form.rules = gallery
    deliveryTimerupdate(form, (data) => {
        ElNotification({
            title: '提示',
            message: '物流时效设置成功！',
            type: 'success',
            position: 'bottom-left'
        })
    }, loading)
}

</script>
<style scoped>
.el-form .el-form-item {
    margin-right: 40%;
}

:deep() .el-input-group {
    width: 220px;
}
</style>