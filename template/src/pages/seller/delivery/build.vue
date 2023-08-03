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
							<el-breadcrumb-item>物流</el-breadcrumb-item>
							<el-breadcrumb-item>运费设置</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<el-form :inline="true" class="pd10">
						<el-form-item label="模板名称" :label-width="100">
							<el-input v-model="delivery.name" clearable />
						</el-form-item>
						<el-form-item label="运费规则" :label-width="100">
							<span class="f-gray f-13">除指定地区外，其余地区的运费采用"默认运费"</span>
						</el-form-item>
						<el-form-item v-if="delivery.name" label="配送方式" :label-width="100" style="margin-right: 0;">
							<div v-for="template in delivery.area_fee">
								<p>{{template.name}}</p>
								<div
									:class="['uni-flex uni-row f-13 pd10 border', (template.other_fee && template.other_fee.length > 0) ? 'border-b0' : '']">
									<span>默认运费：</span>
									<el-input v-model="template.default_fee.start_standards" class="small ml5 mr5" />
									<span>件内，</span>
									<el-input v-model="template.default_fee.start_fees" class="small ml5 mr5" />
									<span>元，</span>
									<span>每增加</span>
									<el-input v-model="template.default_fee.add_standards" class="small ml5 mr5" />
									<span>个，</span>
									<span>增加运费</span>
									<el-input v-model="template.default_fee.add_fees" class="small ml5 mr5" />
									<span>元</span>
								</div>
								<div v-if="template.other_fee && template.other_fee.length > 0">
									<el-table :data="template.other_fee" :border="true" :stripe="false"
										v-loading="loading" class="f-13">
										<el-table-column prop="dests" label="配送地区" />
										<el-table-column prop="start_standards" label="首件">
											<template #default="scope">
												<el-input v-model="scope.row.start_standards" />
											</template>
										</el-table-column>
										<el-table-column prop="start_fees" label="首费(元)">
											<template #default="scope">
												<el-input v-model="scope.row.start_fees" />
											</template>
										</el-table-column>
										<el-table-column prop="add_standards" label="续件">
											<template #default="scope">
												<el-input v-model="scope.row.add_standards" />
											</template>
										</el-table-column>
										<el-table-column prop="add_fees" label="续费(元)">
											<template #default="scope">
												<el-input v-model="scope.row.add_fees" />
											</template>
										</el-table-column>
										<el-table-column fixed="right" label="操作" width="160" align="center">
											<template #default="scope">
												<el-button type="primary" size="small" @click="modifyClick(scope.row)"
													plain>修改地区
												</el-button>
												<el-button type="warning" size="small"
													@click="deleteClick(template, scope.$index)" plain>删除
												</el-button>
											</template>
										</el-table-column>
									</el-table>
								</div>
								<div class="f-blue f-13 flex-middle mt5">
									<el-icon :size="16">
										<CirclePlus />
									</el-icon>
									<span @click="addArea(template)" class="ml5 pointer">添加指定地区运费</span>
								</div>
							</div>
						</el-form-item>

						<el-form-item label=" " :label-width="100">
							<el-button type="primary" @click="submit" :loading="loading">提交</el-button>
						</el-form-item>
					</el-form>
				</div>
			</el-col>
		</el-row>
	</div>

	<regioned title="选择地区" :visible="dialogVisible" :original="modifyItem.dest_ids" @close="dialogClose"></regioned>

	<myfoot></myfoot>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useRoute } from 'vue-router'
import { ElNotification } from 'element-plus'
import { isEmpty, redirect } from '@/common/util.js'
import { deliveryRead, deliveryUpdate } from '@/api/delivery.js'

import regioned from '@/components/dialog/region/list.vue'
import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const route = useRoute()
const loading = ref(false)
const delivery = ref({ name: '默认模板', area_fee: { express: { default_fee: {} } } })
const dialogVisible = ref(false)
const modifyItem = ref({})

onMounted(() => {
	deliveryRead({ id: route.params.id }, (data) => {
		if (data) {
			delivery.value = Object.assign(data, { id: data.template_id })
		}
	})
})

const addArea = (template) => {
	if (isEmpty(template.other_fee)) {
		template.other_fee = []
	}
	template.other_fee.push({})
}
const modifyClick = (value) => {
	dialogVisible.value = true
	modifyItem.value = value
}
const deleteClick = (template, value) => {
	template.other_fee.splice(value, 1)
}

const submit = () => {

	deliveryUpdate(delivery.value, (data) => {
		ElNotification({
			title: '提示',
			message: '运费模板设置成功！',
			type: 'success',
			position: 'bottom-left',
			duration: 2000,
			onClose: () => {
				redirect('/seller/delivery/list')
			}
		})
	}, loading)
}
const dialogClose = (value) => {
	dialogVisible.value = false

	if (value) {
		modifyItem.value.dests = value[0].join(',')
		modifyItem.value.dest_ids = value[1].join('|')
	}
}

</script>
<style scoped>
.el-form .el-form-item {
	margin-right: 40%;
}

.el-form .border {
	border: 1px #eee solid;
}

.el-form .border-b0 {
	border-bottom: 0;
}

.el-form .el-input.small {
	width: 100px;
}

.el-form .tips {
	width: 240px;
	background-color: rgb(235, 248, 252);
}
</style>