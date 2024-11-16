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
							<el-breadcrumb-item>评价管理</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20" v-loading="loading">
					<el-table :data="gallery" :border="false" :stripe="false">
						<el-table-column type="selection" />
						<el-table-column width="320" label="商品信息">
							<template #default="scope">
								<el-row>
									<el-col :span="5"><img :src="scope.row.goods_image" width="50" height="50"
											style="margin-top:4px"></el-col>
									<el-col :span="19" class="l-h17">
										<p class="line-clamp-2">{{ scope.row.goods_name }}</p>
										<p v-if="scope.row.specification" class="f-gray f-12">{{ scope.row.specification
										}}
										</p>
										<p class="mt5">{{ currency(scope.row.price) }} x {{ scope.row.quantity }}</p>
									</el-col>
								</el-row>
							</template>
						</el-table-column>
						<el-table-column label="评价" width="80">
							<template #default="scope">
								<span v-if="scope.row.evaluation > 3" class="f-green">好评</span>
								<span v-else-if="scope.row.evaluation < 3" class="f-red">差评</span>
								<span v-else>中评</span>
							</template>
						</el-table-column>
						<el-table-column label="内容" width="440">
							<template #default="scope">
								<p v-if="scope.row.comment" class="l-h17">{{ scope.row.comment }}</p>
								<p v-else class="f-gray">买家没有填写评价内容</p>
								<div v-if="scope.row.images">
									<img v-for="image in scope.row.images" :src="image" width="60" height="60"
										class="mt10 mr10 mt10">
								</div>
								<p v-if="scope.row.reply_comment" class="mt5 f-green">商家回复：{{ scope.row.reply_comment }}
								</p>
							</template>
						</el-table-column>
						<el-table-column prop="order_sn" label="订单编号" width="110" sortable />
						<el-table-column prop="evaluation_time" label="评价时间" width="100" sortable />
						<el-table-column fixed="right" label="操作" width="110" align="center">
							<template #default="scope">
								<router-link class="rlink f-blue mb5"
									:to="'/seller/order/detail/' + scope.row.order_id">
									查看订单</router-link>

								<el-button v-if="!scope.row.reply_comment" class="mb5" type="warning" size="small"
									@click="replyClick(scope.$index)" plain>
									回复评价
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

	<el-dialog v-model="dialogVisible" title="回复评价" :width="600" :center="true" :draggable="true"
		:destroy-on-close="true" :close-on-click-modal="false" :before-close="close">
		<el-form>
			<el-form-item label="买家评价：">
				<p class="f-blue l-h17">{{ gallery[modifyIndex].comment || '暂无评价内容' }}</p>
			</el-form-item>
			<el-form-item label="回复评价：">
				<el-input v-model="form.reply_comment" :rows="2" type="textarea" placeholder="请填写评价内容" />
			</el-form-item>
		</el-form>
		<template #footer>
			<el-button @click="close">关闭</el-button>
			<el-button type="primary" @click="submit" :loading="loading">提交</el-button>
		</template>
	</el-dialog>

	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { sellerOrderEvaluates, orderReplyevaluate } from '@/api/order.js'
import { currency } from '@/common/util.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'
import { ElMessage } from 'element-plus'

const loading = ref(false)
const dialogVisible = ref(false)
const gallery = ref([])
const pagination = ref({})
const form = reactive({})
const modifyIndex = ref(0)

onMounted(() => {
	getList()
})

const replyClick = (value) => {
	modifyIndex.value = value
	dialogVisible.value = true
}
const submit = () => {
	orderReplyevaluate({ order_id: gallery.value[modifyIndex.value].order_id, spec_id: gallery.value[modifyIndex.value].spec_id, reply_comment: form.reply_comment }, (data) => {
		ElMessage.success('回复评价成功')
		dialogVisible.value = false
		gallery.value[modifyIndex.value].reply_comment = form.reply_comment
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
	sellerOrderEvaluates(Object.assign(form, params), (data) => {
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
