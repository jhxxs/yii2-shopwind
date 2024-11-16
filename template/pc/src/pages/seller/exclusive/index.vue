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
							<el-breadcrumb-item>营销</el-breadcrumb-item>
							<el-breadcrumb-item>手机专享</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<h3 class="pd10 mb20">手机专享优惠</h3>

					<el-form :inline="true" class="pd10">
						<el-form-item label="折扣优惠(默认)" :label-width="100">
							<el-input v-model="form.discount" clearable />
							<span class="f-gray ml5">折，填写0.01-9.99折</span>
						</el-form-item>
						<el-form-item label="减价优惠" :label-width="100">
							<el-input v-model="form.decrease" clearable />
							<span class="f-gray ml5">元</span>
						</el-form-item>
						<el-form-item label="启用" :label-width="100">
							<el-switch v-model="form.status" :active-value="1" :inactive-value="0" />
						</el-form-item>

						<el-form-item label=" " :label-width="100">
							<p class="l-h17 f-12 f-blue tips pd10">用户通过手机等移动设备下单，可以享受一定的减价或折扣优惠，建议设置折扣优惠</p>
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
import { ElNotification } from 'element-plus'
import { exclusiveRead, exclusiveUpdate } from '@/api/exclusive.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const loading = ref(false)
const form = reactive({ status: 1 })

onMounted(() => {
	exclusiveRead(null, (data) => {
		Object.assign(form, data)
	})
})

const submit = () => {
	exclusiveUpdate(form, (data) => {
		ElNotification({
			title: '提示',
			message: '优惠设置成功！',
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

.el-form .el-input {
	width: 220px;
}

.el-form .tips {
	width: 240px;
	background-color: rgb(235, 248, 252);
}
</style>