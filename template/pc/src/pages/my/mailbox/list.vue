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
							<el-breadcrumb-item>账户</el-breadcrumb-item>
							<el-breadcrumb-item>短消息</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<el-form :inline="true">
							<el-form-item label="标题">
								<el-input v-model="form.title" clearable />
							</el-form-item>
							<el-form-item>
								<el-button @click="queryClick" type="primary" class="f-13">查询</el-button>
							</el-form-item>
						</el-form>
					</div>
				</div>
				<div v-loading="loading">
					<div v-if="gallery.length > 0">
						<div v-for="item in gallery" class="round-edge pd10 bgf mt20 ">
							<h3 class="f-14 pl10 pt10 pr10 title">{{ item.title }}</h3>
							<el-divider />
							<div class="pl10 pr10" v-html="item.content"></div>
						</div>
						<div class="mt20 mb20">
							<el-pagination v-model:currentPage="pagination.page" v-model:page-size="pagination.page_size"
								:page-sizes="[10, 50, 100, 200]" :background="true"
								:layout="pagination.page_count > 1 ? 'total, sizes,prev,pager,next' : 'total, sizes'"
								:total="pagination.total" @size-change="handleSizeChange"
								@current-change="handleCurrentChange" :hide-on-single-page="false" class="center" />
						</div>
					</div>
					<el-empty v-else-if="!loading" description="没有数据" />
				</div>
			</el-col>
		</el-row>
	</div>

	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { mailboxList } from '@/api/mailbox.js'

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
	mailboxList(Object.assign(form, params), (data) => {
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
</style>
