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
							<el-breadcrumb-item>积分记录</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<el-form :inline="true">
							<el-form-item label="积分类型">
								<el-select v-model="form.type" @change="queryClick" placeholder="不限制" clearable>
									<el-option label="收入" value="income" />
									<el-option label="消费" value="pay" />
									<el-option label="冻结" value="frozen" />
								</el-select>
							</el-form-item>
							<div class="block">
								<el-form-item label="订单编号" style="width:300px">
									<el-input v-model="form.order_sn" clearable/>
								</el-form-item>
								<el-form-item>
									<el-button @click="queryClick" type="primary" class="f-13">查询</el-button>
								</el-form-item>
							</div>
						</el-form>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20" v-loading="loading">
					<el-table :data="gallery" :border="false" :stripe="false">
						<el-table-column type="selection" />
						<el-table-column prop="name" width="120" label="名称" />
						<el-table-column label="积分变化" width="150">
							<template #default="scope">
								<strong v-if="scope.row.changes > 0" class="f-price f-green">+{{ scope.row.changes
								}}</strong>
								<strong v-else class="f-price f-c60">{{ scope.row.changes }}</strong>
							</template>
						</el-table-column>
						<el-table-column label="余额" width="150">
							<template #default="scope">
								<strong class="f-price">{{ scope.row.balance }}</strong>
							</template>
						</el-table-column>
						<el-table-column label="订单编号" width="200">
							<template #default="scope">
								<span v-if="scope.row.order_sn" class="f-price">{{ scope.row.order_sn }}</span>
							</template>
						</el-table-column>
						<el-table-column prop="add_time" label="使用时间" width="100" sortable />
						<el-table-column prop="flag" label="备注" width="210" />
					</el-table>
					<div v-if="pagination.total > 0" class="mt20 mb20">
						<el-pagination v-model:currentPage="pagination.page" v-model:page-size="pagination.page_size"
							:page-sizes="[10, 50, 100, 200]" :background="false"
							layout="total, sizes, prev, pager, next" :total="pagination.total"
							@size-change="handleSizeChange" @current-change="handleCurrentChange"
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
import { myIntegralList } from '@/api/integral.js'

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

const handleSizeChange = (value) => {
	getList({ page_size: value })
}
const handleCurrentChange = (value) => {
	getList({ page: value, page_size: pagination.value.page_size })
}
function getList(params) {
	myIntegralList(Object.assign(form, params), (data) => {
		gallery.value = data.list
		pagination.value = data.pagination
	}, loading)
}
</script>

<style scoped>
.el-table,
.el-form-item {
	font-size: 13px;
}

.el-table__header-wrapper .el-table-column--selection .el-checkbox {
	vertical-align: middle;
}

</style>
