<template>
    <myhead></myhead>
    <div class="w">
        <div class="round-edge bgf mt10 center flex-middle" style="height: 250px;">
            <el-button v-if="!visitor" size="large" class="f-16 f-blue" loading>授权登录中...</el-button>
            <div v-else>
                <el-icon :size="50" class="f-green">
                    <SuccessFilled />
                </el-icon>
                <p class="f-gray mt5">登录成功！</p>
            </div>
        </div>
    </div>
    <myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { redirect, isEmpty } from '@/common/util.js'
import { userLogin } from '@/api/user.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'

const route = useRoute()
const form = ref({ logintype: '' })
const visitor = ref(null)

onMounted(() => {
    form.logintype = route.query.state || ''
    form.code = route.query.code || ''

    if (form.logintype == 'alipay') {
        form.code = route.query.auth_code
    }

    userLogin(form, (data) => {
        visitor.value = data || null
        setTimeout(() => {
            let url = decodeURIComponent(route.query.redirect)
            if (isEmpty(url)) {
                url = localStorage.getItem('redirect')
                localStorage.removeItem('redirect')
            }
            redirect(isEmpty(url) ? '/' : url)
        }, 2000)

    })
})
</script>
<style scoped>
.el-button {
    border: 0;
}
</style>