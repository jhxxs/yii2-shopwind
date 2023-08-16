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
                            <el-breadcrumb-item>账户</el-breadcrumb-item>
                            <el-breadcrumb-item>修改手机号</el-breadcrumb-item>
                        </el-breadcrumb>
                    </div>
                </div>
                <div class="round-edge pd10 bgf mt20">
                    <h3 class="pd10 mb20">修改手机号</h3>
                    <el-form :inline="true" class="pd10">
                        <el-form-item label="新手机号" :label-width="100">
                            <el-input v-model="form.phone_mob" onkeyup="value=value.replace(/[^\d]/g,'')" maxlength="11" />
                        </el-form-item>
                        <el-form-item label="验证码" :label-width="100">
                            <el-input v-model="form.verifycode" style="width:110px;" />
                            <el-button v-if="seconds == 0" @click="send" class="ml10">获取验证码</el-button>
                            <el-button v-else class="ml10" disabled>{{ seconds }}s后重发</el-button>
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
import { userEditPhone } from '@/api/user.js'
import { smsSend } from '@/api/sms.js'
import { countDown } from '@/common/util.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const loading = ref(false)
const seconds = ref(0)
const form = reactive({ phone_mob: '', verifycode: '' })

onMounted(() => {
    let visitor = JSON.parse(localStorage.getItem('visitor'))
    form.phone_mob = visitor.phone_mob || ''
})

const submit = () => {
    userEditPhone(form, (data) => {
        seconds.value = 0
        ElNotification({
            title: '提示',
            message: '手机号修改成功！',
            type: 'success',
            position: 'bottom-left'
        })
    }, loading)
}

const send = () => {
    smsSend({ phone_mob: form.phone_mob, purpose: 'verifycode' }, (data) => {
        countDown(120, (value) => { seconds.value = value })
        ElMessage.success('验证码发送成功')
    })
}
</script>
<style scoped>
.el-form .el-form-item {
    margin-right: 40%;
}

.el-form .el-input {
    width: 220px;
}
</style>