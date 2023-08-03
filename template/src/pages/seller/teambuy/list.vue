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
							<el-breadcrumb-item>拼团管理</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<el-form :inline="true">
							<el-form-item label="拼团状态">
								<el-select v-model="form.status" @change="queryClick" placeholder="不限制" clearable>
									<el-option label="进行中" value="going" />
									<el-option label="已失效" value="ended" />
								</el-select>
							</el-form-item>
							<el-form-item label="关键词">
								<el-input v-model="form.keyword" clearable />
							</el-form-item>
							<el-form-item>
								<el-button @click="queryClick" type="primary" class="f-13">查询</el-button>
							</el-form-item>
						</el-form>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20" v-loading="loading">
					<el-button class="ml10 mb10" type="primary" @click="redirect('/seller/teambuy/build')">新增拼团
					</el-button>
					<el-table :data="gallery" :border="false" :stripe="false">
						<el-table-column type="selection" />
						<el-table-column width="350" label="商品信息">
							<template #default="scope">
								<el-row>
									<el-col :span="7"><img :src="scope.row.goods_image" width="50" height="50"></el-col>
									<el-col :span="17" class="l-h17">
										<p class="line-clamp-2">{{ scope.row.goods_name }}</p>
									</el-col>
								</el-row>
							</template>
						</el-table-column>
						<el-table-column label="原价" width="100">
							<template #default="scope">
								<span>{{ currency(scope.row.price) }}</span>
							</template>
						</el-table-column>
						<el-table-column label="拼团价" width="100">
							<template #default="scope">
								<strong>{{ currency(scope.row.teamPrice) }}</strong>
							</template>
						</el-table-column>
						<el-table-column prop="name" label="活动标题" width="160">
							<template #default="scope">
								<span>{{ scope.row.title }}</span>
							</template>
						</el-table-column>

						<el-table-column label="状态" width="100">
							<template #default="scope">
								<p v-if="scope.row.status == 1" class="l-h17 f-green">进行中</p>
								<p v-else class="l-h17 f-gray">已失效</p>
							</template>
						</el-table-column>
						<el-table-column fixed="right" label="操作" width="130" align="center">
							<template #default="scope">
								<el-button @click="redirect('/seller/teambuy/build/' + scope.row.id)" type="primary"
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
import { sellerTeambuyList, teambuyDelete } from '@/api/teambuy.js'
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

const deleteClick = (value) => {
	ElMessageBox.confirm('您确定要将该拼团商品吗？', '提示', {
		confirmButtonText: '确定',
		type: 'warning'
	}).then(() => {
		teambuyDelete(gallery.value[value], (data) => {
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
function getList(params = []) {
	sellerTeambuyList(Object.assign(form, params), (data) => {
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
