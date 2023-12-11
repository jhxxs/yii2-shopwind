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
							<el-breadcrumb-item>资产</el-breadcrumb-item>
							<el-breadcrumb-item>钱包</el-breadcrumb-item>
							<el-breadcrumb-item>提现申请</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<h3>余额转出</h3>
						<el-form :inline="true" class="form">
							<el-form-item class="mt10" label="提现到" :label-width="150">
								<el-select v-model="form.drawtype">
									<el-option label="支付宝钱包" value="alipay" />
									<el-option label="银行卡" value="bank" />
								</el-select>
							</el-form-item>
							<el-form-item v-if="form.drawtype == 'bank'" class="mt10" label="银行卡" :label-width="150">
								<el-select v-model="form.index" @change="change">
									<el-option v-for="(item, index) in banks" :key="index"
										:label="item.bank + ' ' + item.account" :value="index" />
								</el-select>
								<p class="w-full"><router-link to="/my/bank/build"
										class="rlink f-blue f-13">设置提现银行卡</router-link></p>
							</el-form-item>
							<el-form-item v-if="form.drawtype == 'alipay'" class="mt10" label="支付宝账户" :label-width="150">
								<el-input v-model="form.account" placeholder="shopwind@qq.com" />
								<el-tag class="mt10" type="warning" size="large">
									支付宝登录账号，邮箱或手机号格式
								</el-tag>
							</el-form-item>
							<el-form-item class="mt10" label="真实姓名" :label-width="150">
								<el-input v-model="form.name" />
								<el-tag class="mt10" size="large">
									与账户一致的真实姓名
								</el-tag>
							</el-form-item>
							<el-form-item class="mt10" label="提现金额" :label-width="150">
								<el-input v-model="form.money" style="width:100px;" /><span class=" ml10">元</span>
							</el-form-item>
							<el-form-item class="mt10" label=" " :label-width="150">
								<el-button type="primary" @click="submit" :loading="loading" :disabled="loading">
									下一步</el-button>
							</el-form-item>
						</el-form>
					</div>
				</div>
			</el-col>
		</el-row>
	</div>

	<el-dialog v-model="dialogVisible" title="提现确认" :width="340" :center="true" :draggable="true" :destroy-on-close="true"
		:close-on-click-modal="false" :before-close="close">
		<div class="center">
			<p>提现</p>
			<p class="f-25 mt5">{{ currency(form.money) }}</p>
			<p class="uni-flex uni-row width-between f-12 f-gray bt pt10 mt20">
				<text>服务费</text>
				<span>{{ currency(form.money * setting.drawal_rate) }}</span>
			</p>
			<p class="uni-flex uni-row width-between f-12 f-gray">
				<text>费率</text>
				<span>{{ setting.drawal_rate * 100 + '%' }}</span>
			</p>
		</div>
		<el-form class="mt20">
			<el-form-item label="支付密码" style="margin-bottom: 0;">
				<el-input v-model="form.password" type="password" placeholder="支付密码" clearable />
				<p class="f-12 f-c60 l-h17 mt5">请输入站内钱包账户的支付密码<br>（非银行卡密码）</p>
			</el-form-item>
		</el-form>
		<template #footer>
			<el-button type="primary" @click="confirm" :loading="!loading">确定
			</el-button>
		</template>
	</el-dialog>

	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessageBox, ElMessage } from 'element-plus'
import { depositDrawal, depositSetting } from '@/api/deposit.js'
import { bankList } from '@/api/bank.js'
import { redirect, currency } from '@/common/util.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const loading = ref(false)
const form = reactive({ drawtype: 'alipay', money: 100 })
const banks = ref([])
const setting = ref({})
const dialogVisible = ref(false)

onMounted(() => {
	bankList(null, (data) => {
		banks.value = data.list
		if (data.list.length > 0) {
			form.name = data.list[0].name
		}
	})

	depositSetting(null, (data) => {
		setting.value = data
		console.log(data)
	})
})

const confirm = () => {
	if (!form.password) {
		return ElMessage.warning('支付密码不能为空')
	}

	if (form.drawtype == 'bank') {
		form.bank = banks.value[form.index].bank
		form.name = banks.value[form.index].name
		form.account = banks.value[form.index].account
	}
	depositDrawal(form, (data) => {
		dialogVisible.value = false
		ElMessageBox({
			title: '提现已提交',
			type: 'success', message: '提现申请已提交，平台将在2个工作日后审核完成！', showCancelButton: false, beforeClose: (action, instance, done) => {
				redirect('/deposit/trade/detail/' + data.tradeNo)
				done()
			}
		})
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
const change = (value) => {
	form.name = banks.value[value].name
}

</script>

<style scoped>
.form {
	margin-top: 60px;
	width: 500px;
}

.el-form .el-input {
	width: 205px;
}

.el-form .el-radio__inner {
	display: none;
}
</style>
