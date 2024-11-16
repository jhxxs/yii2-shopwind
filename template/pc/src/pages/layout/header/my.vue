<template>
	<el-affix :offset="0" class="relative" style="z-index: 2001;">
		<div class="header pt10 pb10">
			<div class="uni-flex uni-row w pt5 pb5 width-between">
				<div class="uni-flex uni-row flex-middle ml5">
					<router-link to="/" class="rlink">
						<!--<img class="block" height="24" src="@/assets/images/logo.png">-->
						<img class="block" height="24" :src="site.logo">
					</router-link>
					<span class="ml10 mr10 f-20">·</span>
					<span class="f-15 line-clamp-1">用户中心</span>
				</div>
				<div class="uni-flex uni-row word-break-all">
					<div v-if="visitor.userid" class="flex-middle mr5">
						<el-popover :width="100">
							<template #reference>
								<p class="vertical-middle">
									<el-avatar :size="36" :src="visitor.portrait" />
									<span class="ml5 mr5">{{ visitor.nickname || visitor.username }}</span>
									<el-icon>
										<arrow-down />
									</el-icon>
								</p>
							</template>
							<div class="uni-flex uni-column center">
								<router-link to="/my/setting/profile" class="rlink mb10">修改头像</router-link>
								<router-link to="/my/setting/password" class="rlink mb10">修改密码</router-link>
								<router-link to="/my/setting/phone" class="rlink mb10">手机设置</router-link>
								<p @click="userLogout" class="rlink">安全退出</p>
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
import { userLogout } from '@/api/user.js'
import { siteRead } from '@/api/site.js'

const visitor = ref({})
const site = ref({ logo: '' })

onMounted(() => {
	visitor.value = JSON.parse(localStorage.getItem('visitor')) || {}

	siteRead(null, (data) => {
		site.value.logo = data.site_logo
	})
})

</script>

<style scoped>
.header {
	background-color: #f7f7f7;
}

.image {
	width: 145px;
	height: 145px;
}
</style>
