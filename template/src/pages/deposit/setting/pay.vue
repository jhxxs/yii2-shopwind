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
                            <el-breadcrumb-item>资产</el-breadcrumb-item>
                            <el-breadcrumb-item>支付设置</el-breadcrumb-item>
                        </el-breadcrumb>
                    </div>
                </div>
                <div class="round-edge pd10 bgf mt20">
                    <h3 class="pd10 mb20">支付密码</h3>
                    <el-form :inline="true" class="pd10">
                        <el-form-item label="设置密码" :label-width="100">
                            <el-input v-model="form.password" type="password" />
                        </el-form-item>
                        <el-form-item label="确认密码" type="password" :label-width="100">
                            <el-input v-model="form.confirmPassword" type="password" />
                        </el-form-item>
                        <el-form-item label="启用余额支付" :label-width="100">
                            <el-tooltip class="box-item" effect="dark" content="开启后才可以使用余额进行支付" placement="top-start">
                                <el-switch v-model="form.pay_status" :active-value=1 :inactive-value=0 />
                            </el-tooltip>
                        </el-form-item>
                        <el-form-item label="验证码" :label-width="100">
                            <el-input v-model="form.verifycode" style="width:110px;" />
                            <el-button v-if="seconds == 0" @click="send" class="ml10">获取验证码</el-button>
                            <el-button v-else class="ml10" disabled>{{ seconds }}s后重发</el-button>
                        </el-form-item>
                        <el-form-item label=" " :label-width="100">
                            <p class="f-12 f-red">验证码发送至手机号：{{ visitor.phone_mob || '' }}</p>
                        </el-form-item>
                        <el-form-item label=" " :label-width="100">
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
import { ElMessage, ElNotification } from 'element-plus'
import { depositUpdate } from '@/api/deposit.js'
import { smsSend } from '@/api/sms.js'
import { countDown } from '@/common/util.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const loading = ref(false)
const seconds = ref(0)
const visitor = ref({})
const form = ref({})

onMounted(() => {
    visitor.value = JSON.parse(localStorage.getItem('visitor'))
})

const submit = () => {
    depositUpdate(form.value, (data) => {
        ElNotification({
            title: '提示',
            message: '支付密码设置成功！',
            type: 'success',
            position: 'bottom-left'
        })
        seconds.value = 0
    }, loading)
}

const send = () => {
    smsSend({ phone_mob: visitor.value.phone_mob, purpose: 'verifycode' }, (data) => {
        countDown(120, (value) => { seconds.value = value })
        ElMessage.success('验证码发送成功')
    })
}
</script>
<style scoped>
.el-form .el-form-item {
    margin-left: 60px;
    margin-right: 40%;
}

.el-form .el-input {
    width: 220px;
}
</style>