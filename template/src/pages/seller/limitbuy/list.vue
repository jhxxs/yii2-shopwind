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
							<el-breadcrumb-item>秒杀管理</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<el-form :inline="true">
							<el-form-item label="活动状态">
								<el-select v-model="form.status" @change="queryClick" placeholder="不限制" clearable>
									<el-option label="即将开始" value="waiting" />
									<el-option label="进行中" value="going" />
									<el-option label="已失效" value="invalid" />
									<el-option label="已结束" value="ended" />
								</el-select>
							</el-form-item>

							<el-form-item label="关键词">
								<el-input v-model="form.keyword" clearable />
							</el-form-item>
							<div class="block">
								<el-form-item label="活动时间">
									<el-date-picker v-model="form.daterange" type="datetimerange" range-separator="-"
										start-placeholder="开始" end-placeholder="结束" format="YYYY-MM-DD HH:mm:ss"
										value-format="YYYY-MM-DD HH:mm:ss" @change="changeClick" />
								</el-form-item>
								<el-form-item>
									<el-button @click="queryClick" type="primary" class="f-13">查询</el-button>
								</el-form-item>
							</div>
						</el-form>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20" v-loading="loading">
					<el-button class="ml10 mb10" type="primary" @click="redirect('/seller/limitbuy/build')">新增秒杀
					</el-button>
					<el-table :data="gallery" :border="false" :stripe="false">
						<el-table-column type="selection" />
						<el-table-column width="280" label="商品信息">
							<template #default="scope">
								<el-row>
									<el-col :span="7">
										<router-link :to="'/goods/detail/' + scope.row.goods_id" class="rlink">
											<img :src="scope.row.goods_image" width="50" height="50">
										</router-link>
									</el-col>
									<el-col :span="17" class="l-h17">
										<router-link :to="'/goods/detail/' + scope.row.goods_id"
											class="rlink line-clamp-2">{{ scope.row.goods_name }}</router-link>
									</el-col>
								</el-row>
							</template>
						</el-table-column>
						<el-table-column label="原价" width="100">
							<template #default="scope">
								<span>{{ currency(scope.row.price) }}</span>
							</template>
						</el-table-column>
						<el-table-column label="促销价" width="100">
							<template #default="scope">
								<strong>{{ currency(scope.row.promotion.price) }}</strong>
							</template>
						</el-table-column>
						<el-table-column prop="name" label="活动标题" width="100">
							<template #default="scope">
								<span>{{ scope.row.promotion.name }}</span>
							</template>
						</el-table-column>
						<el-table-column label="抢购进度" width="130">
							<template #default="scope">
								<el-progress :show-text="false" :text-inside="true" stroke-width="8"
									:percentage="scope.row.promotion.progress * 100" />
								<span class="f-12 f-gray l-h17">{{ scope.row.promotion.sales }}/{{
										scope.row.promotion.sales + scope.row.promotion.stocks
								}}</span>
							</template>
						</el-table-column>
						<el-table-column label="状态" width="100">
							<template #default="scope">
								<p class="l-h17">
									<text v-if="scope.row.status == 'ended'" class="f-gray">已结束</text>
									<text v-else-if="scope.row.status == 'price_invalid'" class="f-red">价格不合理</text>
									<text v-else-if="scope.row.status == 'invalid'" class="f-red">已失效</text>
									<text v-else-if="scope.row.status == 'going'" class="f-green">进行中</text>
									<text v-else class="f-blue">即将开始</text>
								</p>
							</template>
						</el-table-column>
						<el-table-column label="开始时间" width="100">
							<template #default="scope">
								<p class="l-h17">{{ scope.row.start_time }}</p>
							</template>
						</el-table-column>
						<el-table-column label="结束时间" width="100">
							<template #default="scope">
								<p class="l-h17">{{ scope.row.end_time }}</p>
							</template>
						</el-table-column>
						<el-table-column fixed="right" label="操作" width="130" align="center">
							<template #default="scope">
								<el-button @click="redirect('/seller/limitbuy/build/' + scope.row.id)" type="primary"
									size="small" class="mr10" plain>编辑
								</el-button>
								<el-button @click="deleteClick(scope.$index)" type="warning" size="small" plain>
									删除
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

	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessageBox, ElMessage } from 'element-plus'
import { sellerLimitbuyList, limitbuyDelete } from '@/api/limitbuy.js'
import { currency, redirect } from '@/common/util.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

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

const changeClick = (value) => {
	form.begin = value ? value[0] : ''
	form.end = value ? value[1] : ''
	getList()
}

const deleteClick = (value) => {
	ElMessageBox.confirm('您确定要删除该秒杀活动吗？', '提示', {
		confirmButtonText: '确定',
		type: 'warning'
	}).then(() => {
		limitbuyDelete(gallery.value[value], (data) => {
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
function getList(params = {}) {
	sellerLimitbuyList(Object.assign(form, params), (data) => {
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

.el-table .el-table-fixed-column--right .el-button+.el-button {
	margin-left: 0;
}
</style>
