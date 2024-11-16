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
							<el-breadcrumb-item>搭配购管理</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<el-form :inline="true">
							<el-form-item label="搭配购状态">
								<el-select v-model="form.status" @change="queryClick" placeholder="不限制" clearable>
									<el-option label="进行中" value="1" />
									<el-option label="已失效" value="0" />
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
					<el-button class="ml10 mb10" type="primary" @click="redirect('/seller/mealbuy/build')">新增搭配购
					</el-button>
					<el-table :data="gallery" :border="false" :stripe="false">
						<el-table-column type="selection" />
						<el-table-column width="420" label="商品信息">
							<template #default="scope">
								<el-row class="pr10 pb5" style="border-right: 1px #eee solid;"
									v-for="item in scope.row.items" :key="item.rec_id">
									<el-col :span="4"><img :src="item.goods_image" width="50" height="50"
											style="margin-top:4px"></el-col>
									<el-col :span="20" class="l-h20">
										<p class="line-clamp-2">{{ item.goods_name }}</p>
										<p v-if="item.specification" class="f-gray f-12">{{ item.specification }}</p>
										<p class="mt5">{{ currency(item.price) }} x 1</p>
									</el-col>
								</el-row>
							</template>
						</el-table-column>
						<el-table-column label="原总价" width="120" align="center">
							<template #default="scope">
								<span v-if="scope.row.total.length > 0" class="f-yahei">
									{{ currency(scope.row.total[0]) }} - {{ currency(scope.row.total[1]) }}</span>
								<span v-else class="f-yahei">{{ currency(scope.row.total) }}</span>
							</template>
						</el-table-column>
						<el-table-column label="搭配价" width="120" align="center">
							<template #default="scope">
								<strong class="f-yahei">{{ currency(scope.row.price) }}</strong>
							</template>
						</el-table-column>
						<el-table-column prop="status" label="状态" width="80">
							<template #default="scope">
								<el-switch v-model="scope.row.status" active-value="1" inactive-value="0" disabled />
							</template>
						</el-table-column>
						<el-table-column prop="title" label="标题" width="100" sortable />
						<el-table-column prop="created" label="创建时间" width="100" sortable />
						<el-table-column fixed="right" label="操作" align="center" width="130">
							<template #default="scope">
								<el-button @click="redirect('/seller/mealbuy/build/' + scope.row.meal_id)"
									type="primary" size="small" class="mr10" plain>编辑
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
import { sellerMealbuyList, mealbuyDelete } from '@/api/mealbuy.js'
import { currency, redirect } from '@/common/util.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

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

const deleteClick = (value) => {
	ElMessageBox.confirm('您确定要将该搭配购商品吗？', '提示', {
		confirmButtonText: '确定',
		type: 'warning'
	}).then(() => {
		mealbuyDelete(gallery.value[value], (data) => {
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
	sellerMealbuyList(Object.assign(form, params), (data) => {
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
