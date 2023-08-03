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
							<el-breadcrumb-item>充值卡列表</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pt10">
						<el-form :inline="true">
							<div class="block">
								<el-form-item label="名称" style="width:300px">
									<el-input v-model="form.keyword" clearable />
								</el-form-item>
								<el-form-item label="充值卡号码" style="width:300px">
									<el-input v-model="form.cardNo" clearable />
								</el-form-item>
								<el-form-item>
									<el-button @click="queryClick" type="primary" class="f-13">查询</el-button>
								</el-form-item>
							</div>
						</el-form>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20" v-loading="loading">
					<div class="mb10 ml10">
						<el-button v-on:click="bindClick" type="primary">+绑定充值卡</el-button>
					</div>
					<el-table :data="gallery" :border="false" :stripe="false">
						<el-table-column type="selection" />
						<el-table-column prop="name" width="150" label="名称" />
						<el-table-column label="充值卡号码" width="250">
							<template #default="scope">
								<strong class="f-price">{{ scope.row.cardNo }}</strong>
							</template>
						</el-table-column>
						<el-table-column label="金额（元）" width="150">
							<template #default="scope">
								<strong class="f-price">{{ scope.row.money }}</strong>
							</template>
						</el-table-column>
						<el-table-column prop="active_time" label="激活时间" width="100" sortable />
						<el-table-column prop="expire_time" label="过期时间" width="100">
							<template #default="scope">
								<span>{{ scope.row.expire_time == 0 ? '-' : scope.row.expire_time }}</span>
							</template>
						</el-table-column>
						<el-table-column label="状态" width="100">
							<template #default="scope">
								<strong v-if="scope.row.tradeNo">已充值</strong>
								<strong v-else-if="scope.row.valid > 0" class="f-green">有效</strong>
								<strong v-else class="f-gray">已失效</strong>
							</template>
						</el-table-column>
						<el-table-column fixed="right" label="操作" width="100" align="center">
							<template #default="scope">
								<router-link v-if="scope.row.tradeNo" :to="'/deposit/trade/detail/' + scope.row.tradeNo"
									class="rlink f-blue">查看</router-link>
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

	<cashcardBind title="绑定充值卡" :visible="dialogVisible" @close="dialogClose"></cashcardBind>

	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { myCashcardList } from '@/api/cashcard.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'
import cashcardBind from '@/components/dialog/cashcard/bind.vue'

const loading = ref(false)
const gallery = ref([])
const pagination = ref({})
const form = reactive({})
const dialogVisible = ref(false)

onMounted(() => {
	getList()
})

const queryClick = () => {
	getList()
}

const bindClick = () => {
	dialogVisible.value = true
}

const handleSizeChange = (value) => {
	getList({ page_size: value })
}
const handleCurrentChange = (value) => {
	getList({ page: value, page_size: pagination.value.page_size })
}
const dialogClose = (value) => {
	dialogVisible.value = false
	if (value) {
		getList()
	}
}
function getList(params) {
	myCashcardList(Object.assign(form, params), (data) => {
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
