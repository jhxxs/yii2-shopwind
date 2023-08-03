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
							<el-breadcrumb-item>交易记录</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<el-form :inline="true">
							<el-form-item label="交易状态">
								<el-radio-group v-model="form.status" @change="queryClick">
									<el-radio-button label="">全部</el-radio-button>
									<el-radio-button label="PENDING">待付款</el-radio-button>
									<el-radio-button label="ACCEPTED">待发货</el-radio-button>
									<el-radio-button label="SHIPPED">已发货</el-radio-button>
									<el-radio-button label="VERIFY">审核中</el-radio-button>
									<el-radio-button label="CLOSED">交易关闭</el-radio-button>
									<el-radio-button label="SUCCESS">交易完成</el-radio-button>
								</el-radio-group>
							</el-form-item>

							<el-form-item label="创建时间">
								<el-radio-group v-model="form.datetype" @change="changeClick($event, 'datetype')">
									<el-radio-button label="">全部</el-radio-button>
									<el-radio-button label="today">今天</el-radio-button>
									<el-radio-button label="yesterday">昨天</el-radio-button>
									<el-radio-button label="last7">最近7天</el-radio-button>
									<el-radio-button label="last30">最近30天</el-radio-button>
									<el-radio-button label="month">本月</el-radio-button>
									<el-radio-button label="year">本年</el-radio-button>
								</el-radio-group>
								<el-date-picker v-model="form.daterange" type="daterange" range-separator="-"
									start-placeholder="开始" end-placeholder="结束" format="YYYY-MM-DD"
									value-format="YYYY-MM-DD" @change="changeClick($event, 'daterange')" class="ml10" />
							</el-form-item>
							<el-form-item label="资金方向">
								<el-select v-model="form.flow" @change="queryClick" placeholder="不限制" clearable>
									<el-option label="收入" value="income" />
									<el-option label="支出" value="outlay" />
								</el-select>
							</el-form-item>
							<el-form-item label="交易号" style="width:300px">
								<el-input v-model="form.tradeNo" clearable />
							</el-form-item>
							<div class="block">
								<el-form-item label="交易标题" style="width:300px;">
									<el-input v-model="form.keyword" clearable />
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
						<el-table-column prop="add_time" label="创建日期" width="100" sortable />
						<el-table-column width="300" label="交易标题">
							<template #default="scope">
								<p>{{ scope.row.title }}</p>
								<p v-if="scope.row.buyer_remark" class="f-gray f-12">{{ scope.row.buyer_remark }}</p>
							</template>
						</el-table-column>
						<el-table-column prop="amount" label="金额（元）" width="140">
							<template #default="scope">
								<p :class="['f-price', scope.row.flow == 'income' ? 'f-green' : 'f-c60']">
									<span v-if="scope.row.flow == 'income'">+</span>
									<span v-else>-</span>
									<strong>{{ currency(scope.row[scope.column.property], 2, '') }}</strong>
								</p>
							</template>
						</el-table-column>
						<el-table-column prop="status" label="订单状态" width="150" sortable>
							<template #default="scope">
								<p class="bold">
									<span v-if="scope.row.status == 'CLOSED'" class="f-gray">
										{{ translator(scope.row.status, 'trade') }}
									</span>
									<span v-else-if="scope.row.status == 'SUCCESS'" class="f-blue">
										{{ translator(scope.row.status, 'trade') }}
									</span>
									<span v-else>{{ translator(scope.row.status, 'trade') }}</span>
								</p>
							</template>
						</el-table-column>
						<el-table-column prop="tradeNo" label="交易号" width="130" sortable />
						<el-table-column prop="bizOrderId" label="商户订单号" width="130" sortable />
						<el-table-column label="对方" width="150">
							<template #default="scope">
								<p v-if="visitor.userid == scope.row.buyer_id">{{ scope.row.store_name || '' }}</p>
								<p v-else>{{ scope.row.buyer_name || '' }}</p>
							</template>
						</el-table-column>

						<el-table-column prop="payment_name" label="支付方式" width="150" sortable />
						<el-table-column prop="pay_time" label="付款时间" width="100" sortable />
						<el-table-column prop="end_time" label="完成时间" width="100" sortable />
						<el-table-column fixed="right" label="操作" width="100" align="center">
							<template #default="scope">
								<router-link class="rlink f-blue mb5"
									:to="'/deposit/trade/detail/' + scope.row.tradeNo">
									查看</router-link>
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
import { depositTradeList } from '@/api/deposit.js'
import { currency, translator } from '@/common/util.js'
import { getToday, getYesterday, getLast7Days, getCurrMonthDays, getCurrYearDays, getLast30Days } from '@/common/moment.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const loading = ref(false)
const gallery = ref([])
const pagination = ref({})
const form = reactive({ queryitem: true })
const visitor = ref({})

onMounted(() => {
	getList()
	visitor.value = JSON.parse(localStorage.getItem('visitor'))
})

const queryClick = () => {
	getList()
}

const changeClick = (value, field) => {
	let range = { starttime: '', endtime: '' }
	if (field == 'datetype') {
		if (value == 'today') {
			range = getToday()
		} else if (value == 'yesterday') {
			range = getYesterday()
		} else if (value == 'month') {
			range = getCurrMonthDays()
		} else if (value == 'year') {
			range = getCurrYearDays()
		} else if (value == 'last7') {
			range = getLast7Days()
		} else if (value == 'last30') {
			range = getLast30Days()
		}
		form.daterange = ''
		form.begin = range.starttime
		form.end = range.endtime
	} else if (field == 'daterange') {
		form.datetype = ''
		form.begin = value ? value[0] + ' 00:00:00' : ''
		form.end = value ? value[1] + ' 23:59:59' : ''
	}
	getList()
}
const handleSizeChange = (value) => {
	getList({ page_size: value })
}
const handleCurrentChange = (value) => {
	getList({ page: value, page_size: pagination.value.page_size })
}
function getList(params) {
	depositTradeList(Object.assign(form, params), (data) => {
		gallery.value = data.list
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

.el-table .el-table-fixed-column--right .el-button+.el-button {
	margin-left: 0;
}
</style>
