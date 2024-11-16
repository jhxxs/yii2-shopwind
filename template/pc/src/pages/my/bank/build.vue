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
							<el-breadcrumb-item>提现设置</el-breadcrumb-item>
							<el-breadcrumb-item>绑定银行卡</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="flex-middle">
						<h3 class="pd10 mb20">添加提现银行卡</h3>
						<el-tag class="mb20 ml5">银行卡信息仅作为站内余额提现时需要，不用于其他用途</el-tag>
					</div>
					<el-form :inline="true" class="form">
						<el-form-item label="开户行" :label-width="150">
							<el-select v-model="form.code">
								<el-option v-for="item in banks" :label="item.bank" :value="item.code" />
							</el-select>
						</el-form-item>
						<el-form-item label="持卡人" :label-width="150">
							<el-input v-model="form.name" class="small" />
							<p class="w-full"><span class="f-c60 mt5 f-12">请绑定持卡人本人的银行卡</span></p>
						</el-form-item>
						<el-form-item label="卡号" :label-width="150">
							<el-input v-model.number="form.account" class="small" placeholder="持卡人银行卡号" />
						</el-form-item>
						<el-form-item label="支行" :label-width="150">
							<el-input v-model="form.area" class="small" placeholder="XXX支行" />
						</el-form-item>
						<el-form-item label=" " :label-width="150" class="pt20">
							<el-button type="primary" @click="submit" :loading="loading" :disabled="loading">提交</el-button>
						</el-form-item>
					</el-form>
				</div>
			</el-col>
		</el-row>
	</div>

	<el-dialog v-model="dialogVisible" title="安全验证" :width="340" :center="true" :draggable="true" :destroy-on-close="true"
		:close-on-click-modal="false" :before-close="close">
		<el-form>
			<el-form-item label="支付密码" style="margin-bottom: 0;">
				<el-input v-model="form.password" type="password" class="small" clearable />
				<p class="f-12 f-c60 l-h17 mt5">请输入站内钱包账户的支付密码<br>（非银行卡密码）</p>
			</el-form-item>
		</el-form>
		<template #footer>
			<el-button type="primary" @click="confirm" :loading="loading == false">确定
			</el-button>
		</template>
	</el-dialog>

	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { bankAdd } from '@/api/bank.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const loading = ref(false)
const banks = reactive([{ code: 'ICBC', bank: '中国工商银行' }, { code: 'ABC', bank: '中国农业银行' }, { code: 'CCB', bank: '中国建设银行' }, { code: 'BOC', bank: '中国银行' }])
const form = reactive({})
const dialogVisible = ref(false)

const confirm = () => {
	if (!form.password) {
		return ElMessage.warning('支付密码不能为空')
	}

	banks.forEach(item => {
		if (item.code == form.code) {
			form.bank = item.bank
		}
	})

	dialogVisible.value = false
	bankAdd(form, (data) => {
		ElMessage.success('设置成功')
	}, loading)
}

const submit = () => {
	loading.value = true
	dialogVisible.value = true
}
const close = (done) => {
	loading.value = false
	done()
}


</script>

<style scoped>
.form {
	margin-top: 50px;
	width: 500px;
}

.el-form .el-input.small {
	width: 205px;
}
</style>
