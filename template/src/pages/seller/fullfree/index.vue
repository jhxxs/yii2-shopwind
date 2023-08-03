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
							<el-breadcrumb-item>满包邮</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<h3 class="pd10 mb20">满包邮设置</h3>

					<el-form :inline="true" class="pd10">
						<el-form-item label="满金额包邮" :label-width="100">
							<el-input v-model="form.amount" clearable />
							<span class="f-gray ml5">元</span>
						</el-form-item>
						<el-form-item label="满件包邮" :label-width="100">
							<el-input v-model="form.quantity" clearable />
							<span class="f-gray ml5">件</span>
						</el-form-item>
						<el-form-item label="启用" :label-width="100">
							<el-switch v-model="form.status" :active-value="1" :inactive-value="0" />
						</el-form-item>

						<el-form-item label=" " :label-width="100">
							<p class="l-h17 f-12 f-blue tips pd10">可设置单笔订单满多少件包邮和商品总额满多少金额包邮，满足其中一项即执行该优惠</p>
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
import { fullfreeRead, fullfreeUpdate } from '@/api/fullfree.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const loading = ref(false)
const form = reactive({ status: 1 })

onMounted(() => {
	fullfreeRead(null, (data) => {
		Object.assign(form, data)
	})
})

const submit = () => {
	fullfreeUpdate(form, (data) => {
		ElNotification({
			title: '提示',
			message: '满包邮设置成功！',
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