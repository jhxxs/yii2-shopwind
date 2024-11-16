<template>
	<myhead></myhead>
	<div class="w" v-loading="loading">
		<div class="round-edge bgf mt10" style="padding: 40px;">
			<h3 class="pb20">用户登录</h3>
			<el-form class="pd10 mt20" style="width: 400px;">
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
					<el-button type="primary" v-on:click="submit" :loading="loading" size="large" class="w-full">{{
						form.logintype == 'password' ? '立即登录' : '登录/注册'
					}}</el-button>
					<p class="mt10 f-c60 center w-full">
						<label v-if="form.logintype == 'password'" @click="form.logintype = 'verifycode'"
							class="pointer">快速注册/登录</label>
						<label v-else @click="form.logintype = 'password'" class="pointer">账号密码登录</label>
					</p>
				</el-form-item>
				<el-form-item label=" " :label-width="100" class="partner flex-middle mb5">
					<p class="w-full center">
						<i @click="connect('qq')" class="iconfont icon-QQ-circle-fill f-blue ml10 mr10 pointer qq" />
						<i @click="connect('weixin')" class="iconfont icon-weixin1 f-green ml10 mr10 pointer" />
						<i @click="connect('alipay')" class="iconfont icon-zhifubao f-blue ml10 mr10 pointer" />
					</p>
				</el-form-item>
			</el-form>
		</div>
	</div>
	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'

import { countDown, redirect } from '@/common/util.js'
import { userLogin, userConnect } from '@/api/user.js'
import { smsSend } from '@/api/sms.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'

const route = useRoute()
const loading = ref(false)
const form = reactive({ logintype: 'password', verifycode: '', verifycodekey: '' })
const timer = reactive({ seconds: 0 })

onMounted(() => {
	if (route.query.redirect) {
		localStorage.setItem('redirect', decodeURIComponent(route.query.redirect))
	}
})

const submit = () => {
	userLogin(form, (data) => {
		ElMessage.success({
			message: '登录成功', duration: 500, onClose: () => {
				redirect(route.query.redirect ? route.query.redirect : '/')
			}
		})
	}, loading)
}
const connect = (value) => {
	userConnect({ logintype: value }, (data) => {
		location.href = data.redirect
	})
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
.partner {
	margin-top: 50px;
}

.partner .iconfont {
	font-size: 25px;
}

.partner .iconfont.qq {
	color: #1677ff
}

:deep() .el-form-item__label {
	margin-top: 3px;
}

:deep() .el-input-group__append,
:deep() .el-input-group__prepend {
	padding: 0 12px;
}
</style>
