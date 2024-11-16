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
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<el-form :inline="true">
							<el-form-item label="银行卡号">
								<el-input v-model="form.account" clearable />
							</el-form-item>
							<el-form-item label="户名">
								<el-input v-model="form.name" clearable />
							</el-form-item>
							<el-form-item>
								<el-button @click="queryClick" type="primary" class="f-13">查询</el-button>
							</el-form-item>
						</el-form>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20" v-loading="loading">
					<div class="mb10 ml10">
						<el-button @click="redirect('/my/bank/build')" type="primary">+绑定银行卡</el-button>
					</div>
					<el-table :data="gallery" :border="false" :stripe="false">
						<el-table-column type="selection" />
						<el-table-column prop="bank" label="银行" />
						<el-table-column prop="account" width="250" label="卡号" />
						<el-table-column prop="name" label="户名" />
						<el-table-column prop="area" label="开户行" />
						<el-table-column fixed="right" label="操作" width="100" align="center">
							<template #default="scope">
								<el-button type="warning" size="small" @click="deleteClick(scope.$index)" plain>删除
								</el-button>
							</template>
						</el-table-column>
					</el-table>
					<div v-if="pagination.total > 0" class="mt20 mb20">
						<el-pagination v-model:currentPage="pagination.page" v-model:page-size="pagination.page_size"
							:page-sizes="[10, 50, 100, 200]" :background="false" layout="total, sizes, prev, pager, next"
							:total="pagination.total" @size-change="handleSizeChange" @current-change="handleCurrentChange"
							:hide-on-single-page="false" class="center" />
					</div>
				</div>
			</el-col>
		</el-row>
	</div>

	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { redirect } from '@/common/util.js'
import { bankList, bankDelete } from '@/api/bank.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const loading = ref(false)
const gallery = ref([])
const pagination = ref({})
const form = reactive({})

onMounted(() => {
	getList()
})

const queryClick = () => {
	getList()
}
const deleteClick = (value) => {
	ElMessageBox.confirm('您确定要删除该条记录吗？', '提示', {
		confirmButtonText: '确定',
		type: 'warning'
	}).then(() => {
		bankDelete(gallery.value[value], (data) => {
			ElMessage.success('删除成功！')
			gallery.value.splice(value, 1)
		})
	}).catch(() => { })
}

const handleSizeChange = (value) => {
	getList({ page_size: value })
}
const handleCurrentChange = (value) => {
	getList({ page: value, page_size: pagination.value.page_size })
}

function getList(params) {
	bankList(Object.assign(form, params), (data) => {
		gallery.value = data.list
		console.log(data)
		pagination.value = data.pagination
	}, loading)
}
</script>

<style>
.el-table,
.el-form-item {
	font-size: 13px;
}

.el-table__header-wrapper .el-table-column--selection .el-checkbox {
	vertical-align: middle;
}
</style>
