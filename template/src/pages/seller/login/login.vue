<template>
	<myhead></myhead>
	<div class="main">
		<div class="w uni-flex uni-row flex-end">
			<div class="round-edge bgf form" v-loading="loading">
				<h3 class="mb20">商家登录</h3>
				<el-form class="pd10 mt10 w-full">
					<el-form-item label="手机号" :label-width="100">
						<el-input v-model="form.phone_mob" size="large" clearable>
							<template #prepend>
								<span>+86</span>
								<span class="f-simsun ml5">></span>
							</template>
						</el-input>
					</el-form-item>
					<el-form-item v-if="form.logintype == 'password'" label="登录密码" :label-width="100">
						<el-input v-model="form.password" type="password" :show-password="true" size="large" clearable />
						<p class="f-gray f-12 w-full">忘记密码？使用 <span @click="form.logintype = 'verifycode'"
								class="f-blue pointer">验证码登录</span></p>
					</el-form-item>
					<el-form-item v-else label="验证码" :label-width="100">
						<el-input v-model="form.verifycode" size="large" clearable>
							<template #append>
								<span v-if="timer.seconds > 0" class="f-c60 f-13">{{ timer.seconds }}秒后重发</span>
								<span v-else @click="sendcode" class="f-c60 f-13 pointer">获取验证码</span>
							</template>
						</el-input>
						<p class="f-gray f-12 w-full">新手机号验证后将自动注册</p>
					</el-form-item>
					<el-form-item label=" " :label-width="100">
						<el-button type="primary" v-on:click="submit" :loading="loading" size="large"
							class="w-full">立即登录</el-button>
						<p class="mt10 f-c60 center w-full">
							<label v-if="form.logintype == 'password'" @click="form.logintype = 'verifycode'"
								class="pointer">验证码登录</label>
							<label v-else @click="form.logintype = 'password'" class="pointer">账号密码登录</label>
						</p>
					</el-form-item>
				</el-form>
			</div>
		</div>
	</div>
	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'

import { countDown, redirect } from '@/common/util.js'
import { userLogin } from '@/api/user.js'
import { smsSend } from '@/api/sms.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'

const route = useRoute()
const loading = ref(false)
const form = reactive({ logintype: 'password', verifycode: '', verifycodekey: '' })
const timer = reactive({ seconds: 0 })

const submit = () => {
	userLogin(form, (data) => {
		ElMessage.success({
			message: '登录成功', duration: 500, onClose: () => {
				if (data.user_info.store_id) {
					redirect(route.query.redirect ? route.query.redirect : '/seller/index')
				} else redirect('/store/apply')
			}
		})
	}, loading)
}

const sendcode = () => {
	smsSend({ phone_mob: form.phone_mob, purpose: 'verifycode' }, (data) => {
		if (data) {
			form.verifycodekey = data.codekey
			countDown(120, (seconds) => {
				timer.seconds = seconds
			})
		}
	})
}

</script>
<style scoped>
:deep() .header {
	background-color: #f7f7f7 !important;
	color: #000;
}

.main {
	padding: 40px 0;
	background: url('@/assets/images/loginbg.png') center center no-repeat;
	background-size: cover;
}

.form {
	width: 400px;
	padding: 40px 80px 40px 40px;
}
</style>
