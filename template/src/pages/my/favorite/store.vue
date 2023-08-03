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
							<el-breadcrumb-item>收藏</el-breadcrumb-item>
							<el-breadcrumb-item>我收藏的店铺</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20" v-loading="loading">
					<el-table :data="gallery" :border="false" :stripe="false">
						<el-table-column type="selection" />
						<el-table-column label="店铺" width="100">
							<template #default="scope">
								<router-link :to="'/store/index/' + scope.row.store_id" class="rlink">
									<img :src="scope.row.store_logo" width="50" height="50" /></router-link>
							</template>
						</el-table-column>
						<el-table-column prop="store_name" width="400" label="名称" />
						<el-table-column label="等级" width="150">
							<template #default="scope">
								<img :src="scope.row.credit_image" />
							</template>
						</el-table-column>
						<el-table-column prop="add_time" label="收藏时间" width="200" sortable />
						<el-table-column fixed="right" label="操作" width="100" align="center">
							<template #default="scope">
								<el-button type="warning" size="small" @click="removeClick(scope.$index)" plain>移除
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

	<el-dialog v-model="dialogVisible" title="取消收藏" :width="400" :center="true" :draggable="true"
		:destroy-on-close="true" :close-on-click-modal="false" :before-close="close">
		<div class="center">您确定要取消收藏该店铺吗？</div>
		<template #footer>
			<el-button @click="close">关闭</el-button>
			<el-button type="primary" @click="submit" :loading="loading">确定</el-button>
		</template>
	</el-dialog>

	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { favoriteStore, uncollectStore } from '@/api/favorite.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const loading = ref(false)
const gallery = ref([])
const pagination = ref({})
const form = reactive({})
const dialogVisible = ref(false)
const modifyIndex = ref(0)

onMounted(() => {
	getList()
})

const removeClick = (value) => {
	dialogVisible.value = true
	modifyIndex.value = value
}

const submit = () => {
	uncollectStore({ store_id: gallery.value[modifyIndex.value].store_id }, (data) => {
		dialogVisible.value = false
		ElMessage.success('移除成功！')
		gallery.value.splice(modifyIndex.value, 1)
	})
}
const close = () => {
	dialogVisible.value = false
}

const handleSizeChange = (value) => {
	getList({ page_size: value })
}
const handleCurrentChange = (value) => {
	getList({ page: value, page_size: pagination.value.page_size })
}
function getList(params) {
	favoriteStore(Object.assign(form, params), (data) => {
		gallery.value = data.list
		console.log(data.list)
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
