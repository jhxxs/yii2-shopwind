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
							<el-breadcrumb-item>订单</el-breadcrumb-item>
							<el-breadcrumb-item>订单管理</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<el-form :inline="true">
							<el-form-item label="订单状态">
								<el-radio-group v-model="form.type" @change="queryClick">
									<el-radio-button label="">全部({{ sells.all || 0 }})</el-radio-button>
									<el-radio-button label="pending">待付款({{ sells.pending || 0 }})</el-radio-button>
									<el-radio-button label="teaming">待成团({{ sells.teaming || 0 }})</el-radio-button>
									<el-radio-button label="accepted">待发货({{ sells.accepted || 0 }})
									</el-radio-button>
									<el-radio-button label="shipped">待收货({{ sells.shipped || 0 }})
									</el-radio-button>
									<el-radio-button label="finished">交易完成({{ sells.finished || 0 }})
									</el-radio-button>
									<el-radio-button label="canceled">交易关闭({{ sells.canceled || 0 }})
									</el-radio-button>
								</el-radio-group>
							</el-form-item>

							<el-form-item label="下单时间">
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
							<el-form-item label="订单类型">
								<el-select v-model="form.otype" @change="queryClick" placeholder="不限制" clearable>
									<el-option label="普通订单" value="normal" />
									<el-option label="拼团订单" value="teambuy" />
									<el-option label="自提订单" value="guidebuy" />
								</el-select>
							</el-form-item>
							<el-form-item label="订单编号" style="width:243px">
								<el-input v-model="form.order_sn" clearable />
							</el-form-item>
							<el-form-item label="收货人信息" style="width:330px">
								<el-input v-model="form.consignee" placeholder="姓名/手机号" clearable />
							</el-form-item>
							<div class="block">
								<el-form-item label="商品类型">
									<el-select v-model="form.gtype" @change="queryClick" placeholder="不限制" clearable>
										<el-option label="实物商品" value="material" />
										<el-option label="服务商品" value="service" />
										<el-option label="虚拟商品" value="virtual" />
									</el-select>
								</el-form-item>
								<el-form-item label="商品名称" style="width:243px">
									<el-input v-model="form.keyword" clearable />
								</el-form-item>
								<el-form-item label="买家用户名">
									<el-input v-model="form.buyer_name" clearable />
								</el-form-item>
								<el-form-item>
									<el-button @click="queryClick" type="primary" class="f-13">查询</el-button>
								</el-form-item>
							</div>
						</el-form>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20" v-loading="loading">
					<el-table ref="multipleTableRef" :data="gallery" :border="false" :stripe="false" @select="selectClick"
						@selectAll="selectClick">
						<el-table-column type="selection" />
						<el-table-column width="420" label="商品信息">
							<template #default="scope">
								<el-row class="pr10 pb5" style="border-right: 1px #eee solid;"
									v-for="item in scope.row.items" :key="item.rec_id">
									<el-col :span="4">
										<router-link :to="'/goods/detail/' + item.goods_id" class="rlink"
											style="margin-top:4px"><img :src="item.goods_image" width="50" height="50">
										</router-link>
									</el-col>
									<el-col :span="20" class="l-h20">
										<router-link :to="'/goods/detail/' + item.goods_id" class="line-clamp-2 rlink">
											<el-tag v-if="scope.row.gtype == 'virtual'" class="f-12">虚拟商品</el-tag>
											<el-tag v-else-if="scope.row.gtype == 'service'" class="f-12"
												type="danger">服务商品</el-tag>
											{{ item.goods_name }}
										</router-link>
										<p v-if="item.specification" class="f-gray f-12">{{ item.specification }}</p>
										<p class="mt5">{{ currency(item.price) }} x {{ item.quantity }}</p>
									</el-col>
								</el-row>
							</template>
						</el-table-column>
						<el-table-column prop="order_amount" label="订单总价" width="150" align="center">
							<template #default="scope">
								<strong class="f-yahei">{{ currency(scope.row[scope.column.property]) }}</strong>
								<p v-if="scope.row.gtype == 'material'" class="f-12">(含运费{{ scope.row.freight }})</p>
							</template>
						</el-table-column>
						<el-table-column prop="buyer_name" label="买家" width="150" sortable />
						<el-table-column prop="status" label="订单状态" width="150">
							<template #default="scope">
								<p class="bold">
									<span v-if="scope.row.status == 0" class="f-gray">
										{{ translator(scope.row.status) }}
									</span>
									<span v-else-if="scope.row.status == 20 || scope.row.status == 11" class="f-red">
										{{ translator(scope.row.status) }}
									</span>
									<span v-else-if="scope.row.status == 19" class="f-blue">
										{{ translator(scope.row.status) }}
									</span>
									<span v-else>{{ translator(scope.row.status) }}</span>
								</p>
								<p v-if="scope.row.refund_id">
									<router-link v-if="scope.row.refund_status == 'SUCCESS'" class="rlink f-blue"
										:to="'/seller/refund/detail/' + scope.row.refund_id">退款成功</router-link>
									<router-link v-else-if="scope.row.refund_status && scope.row.refund_status != 'CLOSED'"
										class="rlink f-c60" :to="'/seller/refund/detail/' + scope.row.refund_id">
										退款中</router-link>
								</p>
							</template>
						</el-table-column>
						<el-table-column prop="payment_name" label="支付方式" width="150" sortable />
						<el-table-column prop="order_sn" label="订单编号" width="110" sortable />
						<el-table-column prop="add_time" label="下单时间" width="100" sortable />
						<el-table-column prop="pay_time" label="付款时间" width="100" sortable />
						<el-table-column prop="ship_time" label="发货时间" width="100" sortable />
						<el-table-column prop="finished_time" label="完成时间" width="100" sortable />
						<el-table-column fixed="right" label="操作" align="center" width="100">
							<template #default="scope">
								<router-link class="rlink f-blue mb5"
									:to="'/seller/order/detail/' + scope.row.order_id">查看订单</router-link>
								<el-button v-if="!(scope.row.pay_time || scope.row.ship_time || scope.row.finished_time)"
									class="mb5" size="small" @click="cancelClick(scope.$index)" plain>取消订单</el-button>

								<!--如果是实物 & 虚拟商品-->
								<div v-if="scope.row.gtype != 'service'">
									<el-button v-if="scope.row.status == 20 && scope.row.gtype == 'material'" class="mb5"
										type="warning" size="small" @click="shipClick(scope.$index)" plain>
										商家发货
									</el-button>
									<el-button v-if="scope.row.status == 20 && scope.row.gtype == 'virtual'" class="mb5"
										type="warning" size="small" @click="shipClick(scope.$index)" plain>
										虚拟发货
									</el-button>
									<el-button v-if="scope.row.status == 30 && scope.row.gtype == 'material'" class="mb5"
										type="warning" size="small" @click="shipClick(scope.$index)" plain>
										修改单号
									</el-button>
								</div>
								<router-link v-if="scope.row.ship_time && scope.row.gtype == 'material'"
									class="rlink f-blue block"
									:to="'/seller/order/logistic/' + scope.row.order_id">查看物流</router-link>
							</template>
						</el-table-column>
					</el-table>
					<div v-if="mulselection.length > 0" class="mt20 center">
						<el-button type="primary" @click="dialogPrintVisible = true">打印订单</el-button>
						<el-button type="primary" @click="dialogExportVisible = true">导出EXCEL</el-button>
					</div>
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

	<shipped v-if="gallery.length > 0" title="发货" :visible="dialogShipVisible" :data="gallery[modifyIndex]"
		@close="dialogClose"></shipped>
	<canceled v-if="gallery.length > 0" title="取消订单" :visible="dialogCancelVisible" :data="gallery[modifyIndex]"
		@close="dialogClose"></canceled>
	<printed v-if="mulselection.length > 0" title="打印订单" :visible="dialogPrintVisible" :data="mulselection"
		@close="dialogClose"></printed>
	<exported v-if="mulselection.length > 0" title="导出EXCEL" :visible="dialogExportVisible" :data="mulselection"
		@close="dialogClose"></exported>

	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { sellerOrderList, sellerOrderRemind } from '@/api/order.js'
import { currency, translator } from '@/common/util.js'
import { getToday, getYesterday, getLast7Days, getCurrMonthDays, getCurrYearDays, getLast30Days } from '@/common/moment.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'
import shipped from '@/components/dialog/order/shipped.vue'
import canceled from '@/components/dialog/order/canceled.vue'
import printed from '@/components/dialog/order/printed.vue'
import exported from '@/components/dialog/order/export.vue'

const loading = ref(false)
const gallery = ref([])
const pagination = ref({})
const form = reactive({ queryitem: true })
const sells = ref({})
const mulselection = ref([])
const dialogShipVisible = ref(false)
const dialogCancelVisible = ref(false)
const dialogPrintVisible = ref(false)
const dialogExportVisible = ref(false)
const modifyIndex = ref(0)
const route = useRoute()

onMounted(() => {

	getList(route.query)
	sellerOrderRemind(null, (data) => {
		sells.value = data
	})
})
const queryClick = () => {
	getList()
}

const shipClick = (value) => {
	dialogShipVisible.value = true
	modifyIndex.value = value
}
const cancelClick = (value) => {
	dialogCancelVisible.value = true
	modifyIndex.value = value
}

const selectClick = (selection, row) => {
	mulselection.value = []
	selection.forEach((item) => {
		mulselection.value.push(item)
	})
}

const dialogClose = (value) => {
	dialogShipVisible.value = false
	dialogCancelVisible.value = false
	dialogPrintVisible.value = false
	dialogExportVisible.value = false
	Object.assign(gallery.value[modifyIndex.value], value ? value : {})
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
	mulselection.value = []
	sellerOrderList(Object.assign(form, params), (data) => {
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

:deep() .el-table__header-wrapper .el-table-column--selection .el-checkbox {
	vertical-align: middle;
}

.el-table .el-table-fixed-column--right .el-button+.el-button {
	margin-left: 0;
}
</style>
