<template>
	<el-affix :offset="0" class="relative" style="z-index: 2001;">
		<div class="header pt10 pb10 mb5 f-white">
			<div class="uni-flex uni-row w pt5 pb5 width-between">
				<div class="uni-flex uni-row flex-middle ml5">
					<router-link to="/" class="rlink">
						<!--<img class="block" height="24" src="@/assets/images/logo.png">-->
						<img class="block" height="24" :src="site.logo">
					</router-link>
					<span class="ml10 mr10 f-20">·</span>
					<span class="f-15 line-clamp-1">商家中心</span>
				</div>
				<div class="uni-flex uni-row word-break-all">
					<div v-if="visitor.userid" class="flex-middle mr5 ml10">
						<el-popover>
							<template #reference>
								<p class="vertical-middle">
									<el-avatar :size="36" :src="visitor.portrait" class="mr5" />
									<span class="mr5">{{ visitor.nickname || visitor.username }}</span>
									<el-icon>
										<arrow-down />
									</el-icon>
								</p>
							</template>
							<div class="uni-flex uni-column center">
								<router-link to="/my/setting/profile" class="rlink mb10">修改头像</router-link>
								<router-link to="/my/setting/password" class="rlink mb10">修改密码</router-link>
								<router-link to="/my/setting/phone" class="rlink mb10">手机设置</router-link>
								<p @click="logout" class="rlink">安全退出</p>
							</div>
						</el-popover>
					</div>
				</div>
			</div>
		</div>
	</el-affix>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { redirect } from '@/common/util.js'
import { userLogout } from '@/api/user.js'
import { siteRead } from '@/api/site.js'

const visitor = ref({})
const qrcode = reactive({ applet: '', ios: '', android: '' })
const site = ref({ logo: '' })

onMounted(() => {
	visitor.value = JSON.parse(localStorage.getItem('visitor')) || {}

	siteRead(null, (data) => {
		Object.assign(qrcode, data.qrcode)
		site.value.logo = data.site_logo
	})
})

const logout = () => {
	userLogout(() => {
		redirect('/seller/login')
	})
}
</script>

<style scoped>
.header {
	background-color: #505458;
}

.image {
	width: 145px;
	height: 145px;
}

:deep() .el-avatar {
	background: none;
}
</style>
