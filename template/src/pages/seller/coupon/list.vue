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
							<el-breadcrumb-item>优惠券管理</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<el-form :inline="true">
							<el-form-item label="状态">
								<el-select v-model="form.available" @change="queryClick" placeholder="不限制" clearable>
									<el-option label="有效" value="1" />
									<el-option label="无效" value="0" />
								</el-select>
							</el-form-item>
							<el-form-item label="名称">
								<el-input v-model="form.keyword" clearable />
							</el-form-item>
							<el-form-item>
								<el-button @click="queryClick" type="primary" class="f-13">查询</el-button>
							</el-form-item>

						</el-form>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20" v-loading="loading">
					<el-button @click="addClick" type="primary" class="f-13 mb10 ml10 mt5" plain>新增优惠券</el-button>
					<el-table :data="gallery" :border="false" :stripe="false">
						<el-table-column type="selection" />
						<el-table-column prop="coupon_name" width="120" label="名称" />
						<el-table-column label="金额(元)" width="80">
							<template #default="scope">
								<strong class="f-price">{{ scope.row.coupon_value }}</strong>
							</template>
						</el-table-column>
						<el-table-column label="使用条件" width="150">
							<template #default="scope">
								<p class="f-price l-h17">单笔订单购满{{ currency(scope.row.min_amount) }}元可用</p>
							</template>
						</el-table-column>
						<el-table-column label="已领用/总量" width="160">
							<template #default="scope">
								<el-progress :show-text="false" :text-inside="true" stroke-width="8"
									:percentage="((scope.row.total - scope.row.surplus) / scope.row.total) * 100" />
								<span class="f-12 f-gray l-h17">{{ (scope.row.total - scope.row.surplus) }}/{{
										scope.row.total
								}}</span>
							</template>
						</el-table-column>
						<el-table-column prop="start_time" label="生效时间" width="100" sortable />
						<el-table-column prop="end_time" label="过期时间" width="100" sortable />
						<el-table-column label="状态" width="120">
							<template #default="scope">
								<strong v-if="scope.row.available > 0" class="f-green">有效</strong>
								<strong v-else class="f-gray">已失效</strong>
							</template>
						</el-table-column>
						<el-table-column fixed="right" label="操作" width="140" align="center">
							<template #default="scope">
								<el-button @click="modifyClick(scope.$index)" type="primary" size="small" plain>编辑
								</el-button>
								<el-button @click="deleteClick(scope.$index)" type="warning" size="small" plain>
									作废
								</el-button>
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

	<build :title="dialog.title" :visible="dialog.visible" :data="dialog.data" @close="dialogClose"></build>

	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessageBox, ElMessage } from 'element-plus'
import { sellerCouponList, couponDelete } from '@/api/coupon.js'
import { currency } from '@/common/util.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'
import build from '@/components/dialog/coupon/build.vue'

const loading = ref(false)
const gallery = ref([])
const pagination = ref({})
const form = reactive({})
const dialog = reactive({ visible: false })
const modifyIndex = ref(0)

onMounted(() => {
	getList()
})

const queryClick = () => {
	getList()
}
const dialogClose = (value) => {
	dialog.visible = false

	if (value && value.coupon_name) {
		if (value.coupon_id) {
			Object.assign(gallery.value[modifyIndex.value], value)
		} else gallery.value.unshift(value)
	}
}
const addClick = () => {
	dialog.title = '创建优惠券'
	dialog.visible = true
	dialog.data = { clickreceive: 1, total: 100, coupon_value: 10, min_amount: 1000 }
}
const modifyClick = (value) => {
	dialog.title = '编辑优惠券'
	dialog.visible = true
	dialog.data = gallery.value[value]
	modifyIndex.value = value
}
const deleteClick = (value) => {
	ElMessageBox.confirm('您确定要将该优惠券作废吗？', '提示', {
		confirmButtonText: '确定',
		type: 'warning'
	}).then(() => {
		couponDelete(gallery.value[value], (data) => {
			ElMessage.success('该优惠券已作废！')
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
	sellerCouponList(Object.assign(form, params), (data) => {
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
</style>
