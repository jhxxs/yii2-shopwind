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
							<el-breadcrumb-item>售后</el-breadcrumb-item>
							<el-breadcrumb-item>退款管理</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<el-form :inline="true">
							<el-form-item label="退款状态">
								<el-select v-model="form.status" @change="queryClick" placeholder="不限制" clearable>
									<el-option label="退款中" value="GOING" />
									<el-option label="退款成功" value="SUCCESS" />
									<el-option label="退款关闭" value="CLOSED" />
								</el-select>
							</el-form-item>
							<el-form-item label="退款编号">
								<el-input v-model="form.refund_sn" clearable />
							</el-form-item>

							<el-form-item label="交易号">
								<el-input v-model="form.tradeNo" clearable />
							</el-form-item>
							<el-form-item label="订单编号">
								<el-input v-model="form.bizOrderId" clearable />
							</el-form-item>
							<el-form-item>
								<el-button @click="queryClick" type="primary" class="f-13" :loading="loading">查询
								</el-button>
							</el-form-item>
						</el-form>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20" v-loading="loading">
					<el-table :data="gallery" :border="false" :stripe="false">
						<el-table-column type="selection" />
						<el-table-column prop="refund_sn" label="退款单号" width="130">
							<template #default="scope">
								<strong>{{ scope.row[scope.column.property] }}</strong>
							</template>
						</el-table-column>
						<el-table-column prop="total_fee" label="订单总价" width="120">
							<template #default="scope">
								{{ currency(scope.row[scope.column.property]) }}
							</template>
						</el-table-column>
						<el-table-column prop="refund_total_fee" label="退款金额" width="120">
							<template #default="scope">
								{{ currency(scope.row[scope.column.property]) }}
							</template>
						</el-table-column>
						<el-table-column prop="buyer_name" label="买家" width="150" sortable />
						<el-table-column prop="status" label="退款状态" width="150">
							<template #default="scope">
								<p class="bold">
									<span v-if="scope.row.status == 'CLOSED'" class="f-gray">
										{{ translator(scope.row.status, 'refund') }}
									</span>
									<span v-else-if="scope.row.status == 'SUCCESS'" class="f-blue">
										{{ translator(scope.row.status, 'refund') }}
									</span>
									<span v-else>{{ translator(scope.row.status, 'refund') }}</span>
								</p>
							</template>
						</el-table-column>
						<el-table-column prop="bizOrderId" label="订单编号" width="110" sortable />
						<el-table-column prop="tradeNo" label="交易号" width="110" sortable />
						<el-table-column prop="created" label="申请时间" width="100" sortable />
						<el-table-column prop="finished" label="完成时间" width="100" sortable />
						<el-table-column fixed="right" label="操作" width="100" align="center">
							<template #default="scope">
								<router-link class="rlink f-blue mb5"
									:to="'/seller/refund/detail/' + scope.row.refund_id">
									退款详情</router-link>
								<el-button v-if="scope.row.status == 11" class="mb5" size="small"
									@click="cancelClick(scope.$index)" plain>取消订单</el-button>
							</template>
						</el-table-column>
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
import { useRoute } from 'vue-router'
import { sellerRefundList } from '@/api/refund.js'
import { currency, translator } from '@/common/util.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const loading = ref(false)
const gallery = ref([])
const pagination = ref({})
const form = reactive({ queryitem: true })
const route = useRoute()

onMounted(() => {
	getList(route.query)
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
	sellerRefundList(Object.assign(form, params), (data) => {
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
