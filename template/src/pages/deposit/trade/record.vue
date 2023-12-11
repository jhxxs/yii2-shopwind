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
							<el-breadcrumb-item>财务明细</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<el-form :inline="true">
							<el-form-item label="付款时间" class="pt5">
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
							<el-form-item label="交易类型">
								<el-select v-model="form.tradeType" @change="queryClick" placeholder="不限制" clearable>
									<el-option label="支付" value="PAY" />
									<el-option label="充值" value="RECHARGE" />
									<el-option label="提现" value="WITHDRAW" />
									<el-option label="转账" value="TRANSFER" />
									<el-option label="服务费" value="SERVICE" />
									<el-option label="返现" value="REGIVE" />
									<el-option label="扣费" value="CHARGE" />
								</el-select>
							</el-form-item>
							<el-form-item label="资金方向">
								<el-select v-model="form.flow" @change="queryClick" placeholder="不限制" clearable>
									<el-option label="收入" value="income" />
									<el-option label="支出" value="outlay" />
								</el-select>
							</el-form-item>
							<el-form-item label="交易号" style="width:317px">
								<el-input v-model="form.tradeNo" clearable/>
							</el-form-item>
							<el-form-item label=" " :label-width="68">
								<el-button @click="queryClick" type="primary" class="f-13">查询</el-button>
							</el-form-item>
						</el-form>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20" v-loading="loading">
					<el-table :data="gallery" :border="false" :stripe="false">
						<el-table-column type="selection" />
						<el-table-column prop="pay_time" label="付款日期" width="100" sortable />
						<el-table-column width="130" label="名称">
							<template #default="scope">{{ scope.row.name || scope.row.title }}</template>
						</el-table-column>
						<el-table-column prop="tradeNo" label="交易号" width="130" sortable />
						<el-table-column prop="bizOrderId" label="商户订单号" width="130" sortable />
						<el-table-column prop="amount" label="收入（元）" width="100">
							<template #default="scope">
								<p v-if="scope.row.flow == 'income'" class="f-price f-green">
									<strong>+{{ currency(scope.row[scope.column.property], 2, '') }}</strong>
								</p>
								<span v-else></span>
							</template>
						</el-table-column>
						<el-table-column prop="amount" label="支出（元）" width="100">
							<template #default="scope">
								<p v-if="scope.row.flow != 'income'" class="f-price f-c60">
									<strong>-{{ currency(scope.row[scope.column.property], 2, '') }}</strong>
								</p>
								<span v-else></span>
							</template>
						</el-table-column>
						<el-table-column prop="balance" label="账户余额（元）" width="150">
							<template #default="scope">
								<strong class="f-price">{{ currency(scope.row[scope.column.property]) }}</strong>
							</template>
						</el-table-column>
						<el-table-column prop="end_time" label="完成时间" width="100" sortable />
						<el-table-column fixed="right" label="操作" width="100" align="center">
							<template #default="scope">
								<router-link class="rlink f-blue mb5" :to="'/deposit/trade/detail/' + scope.row.tradeNo">
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
import { depositRecordList } from '@/api/deposit.js'
import { currency } from '@/common/util.js'
import { getToday, getYesterday, getLast7Days, getCurrMonthDays, getCurrYearDays, getLast30Days } from '@/common/moment.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const loading = ref(false)
const gallery = ref([])
const pagination = ref({})
const form = reactive({ queryitem: true })

onMounted(() => {
	getList()
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
	depositRecordList(Object.assign(form, params), (data) => {
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
