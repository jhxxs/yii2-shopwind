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
							<el-breadcrumb-item>订单</el-breadcrumb-item>
							<el-breadcrumb-item>短信提醒</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<h3 class="pd10 mb20">短信提醒设置</h3>
					<el-form v-if="gallery.length > 0" class="pd10 ml20">
						<el-form-item v-for="item in gallery" :label="item.name" :label-width="140">
							<el-switch v-model="form.scene[item.code]" :active-value="1" :inactive-value="0"
								class="ml20" @click="submit" />
						</el-form-item>
					</el-form>
					<el-empty v-else :image-size="100" />
				</div>
			</el-col>
		</el-row>
	</div>
	<myfoot></myfoot>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ElNotification } from 'element-plus'
import { smsRead, smsScene, smsUpdate } from '@/api/sms.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const loading = ref(false)
const gallery = ref([])
const form = reactive({ scene: {}, state: 1 })

onMounted(() => {
	smsScene(null, (data) => {
		if (data && data.length > 0) {
			gallery.value = data
			smsRead(null, (array) => {
				if (array && array.length > 0) {
					array.forEach((item) => {
						form.scene[item] = 1
					})
				}
			})
		}
	})
})

const submit = () => {
	smsUpdate(form, (data) => {
		ElNotification({
			title: '提示',
			message: '短信设置成功！',
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
</style>