<template>
	<myhead></myhead>
	<div class="main w pt10">
		<div class="round-edge pd10 bgf center">
			<p v-if="message.icon == 'warn'" class="pd10 mb10 mt20 f-c60">
				<el-icon :size="60">
					<WarningFilled />
				</el-icon>
			</p>
			<p v-else-if="message.icon == 'clock'" class="pd10 mb10 mt20 f-green">
				<el-icon :size="60">
					<Clock />
				</el-icon>
			</p>
			<p v-else-if="message.icon == 'info'" class="pd10 mb10 mt20 f-blue">
				<el-icon :size="60">
					<InfoFilled />
				</el-icon>
			</p>
			<p v-else-if="message.icon == 'fail'" class="pd10 mb10 mt20 f-c60">
				<el-icon :size="60">
					<CircleCloseFilled />
				</el-icon>
			</p>
			<p v-else class="pd10 mb10 mt20 f-green">
				<el-icon :size="60">
					<SuccessFilled />
				</el-icon>
			</p>
			<h3 v-if="message.title">{{ message.title }}</h3>
			<div class="pd10 center mb20 f-c55">{{ message.content || '' }}</div>
		</div>
	</div>
	<myfoot></myfoot>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { redirect } from '@/common/util.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'

const route = useRoute()
const message = ref({})

onMounted(() => {

	message.value = JSON.parse(localStorage.getItem('redirectMessage'))
	//localStorage.removeItem('redirectMessage')

	if (route.query.redirect) {
		setTimeout(() => {
			redirect(route.query.redirect)
		}, 5000);
	}
})
</script>

