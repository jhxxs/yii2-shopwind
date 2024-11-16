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
							<el-breadcrumb-item>拼团</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<h3 class="pd10 mb20">拼团设置</h3>

					<el-form :inline="true" class="pd10">
						<el-form-item label="拼团标题" :label-width="100">
							<el-input v-model="teambuy.title" clearable />
						</el-form-item>
						<el-form-item label="成团人数" :label-width="100">
							<el-select v-model="teambuy.people">
								<el-option label="2人团" :value="2" />
								<el-option label="3人团" :value="3" />
							</el-select>
						</el-form-item>
						<el-form-item label="选择商品" :label-width="100">
							<el-button @click="dialog.visible = true" type="primary" plain>
								<text v-if="goods.goods_id">重新选择商品</text>
								<text v-else>选择商品</text>
							</el-button>
						</el-form-item>
						<el-form-item v-if="goods.goods_id" label=" " :label-width="100">
							<div class="uni-flex uni-row">
								<p><img :src="goods.default_image" width="60" height="60" /></p>
								<p class="l-h20 width-surplus ml10">{{ goods.goods_name }}</p>
							</div>
						</el-form-item>

						<el-form-item label=" " :label-width="100" style="margin-right:0;">
							<p class="tips pl10 pr10 pt5 pb5 f-12 f-c60 flex-middle" style="width:100%">
								<el-icon :size="14" class="mr5">
									<WarningFilled />
								</el-icon> 拼团价只支持设置折扣优惠，取值(0.01-9.99)
							</p>
							<el-table :data="gallery.list" :border="true" :stripe="true">
								<el-table-column
									:label="goods.spec_qty > 0 ? (goods.spec_name_1 + ' ' + goods.spec_name_2 || '') : '规格'">
									<template #default="scope">
										<text>{{ goods.spec_qty > 0 ? scope.row.specification : '默认规格' }}</text>
									</template>
								</el-table-column>
								<el-table-column label="库存" width="100">
									<template #default="scope">
										{{ scope.row.stock }}
									</template>
								</el-table-column>
								<el-table-column label="原价" width="100">
									<template #default="scope">
										{{ scope.row.price }}
									</template>
								</el-table-column>
								<el-table-column label="拼团价" width="100">
									<template #default="scope">
										{{ (scope.row.price * (scope.row.discount || 10) * 0.1).toFixed(2) }}
									</template>
								</el-table-column>
								<el-table-column label="折扣" width="170">
									<template #default="scope">
										<el-input-number v-model="scope.row.discount" controls-position="right"
											:step="0.01" :min="0.01" :max="9.99">
										</el-input-number>
									</template>
								</el-table-column>
							</el-table>
						</el-form-item>
						<el-form-item label=" " :label-width="100" class="mt10">
							<el-button type="primary" @click="submit" :loading="loading"><text
									class="ml20 mr20">提交</text></el-button>
						</el-form-item>
					</el-form>
				</div>
			</el-col>
		</el-row>
	</div>

	<selector title="选择商品" :visible="dialog.visible" @close="dialogClose" :selected="dialog.selected" :limit="1">
	</selector>

	<myfoot></myfoot>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ElMessage, ElNotification } from 'element-plus'
import { useRoute } from 'vue-router'
import { goodsRead, goodsSpecs } from '@/api/goods.js'
import { teambuyRead, teambuyAdd, teambuyUpdate } from '@/api/teambuy.js'

import router from '@/router'
import selector from '@/components/dialog/goods/list.vue'
import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const route = useRoute()
const loading = ref(false)
const dialog = reactive({ visible: false, selected: [] })
const teambuy = reactive({ title: '拼团', people: 2 })
const gallery = reactive({ list: [] })
const goods = ref({})

onMounted(() => {
	if (route.params.id > 0) {
		teambuyRead({ id: route.params.id }, (data) => {
			if (data) {
				Object.assign(teambuy, data)
				dialog.selected = [parseInt(teambuy.goods_id)]
				query()
			}
		})
	}
})

const submit = () => {

	if (gallery.list.length < 1) {
		return ElMessage.warning('请选择商品')
	}

	let rules = {}
	for (let index in gallery.list) {
		let item = gallery.list[index]
		rules[item.spec_id] = {}
		if (item.discount && Number(item.discount) > 0 && Number(item.discount) < 10) {
			rules[item.spec_id].discount = Number(item.discount)
		} else {
			return ElMessage.warning('拼团折扣设置不合理，请检查！')
		}
	}

	if (teambuy.id > 0) {
		teambuyUpdate(Object.assign(teambuy, { specs: rules, goods_id: goods.value.goods_id }), (data) => {
			ElNotification({
				title: '提示',
				message: '拼团设置成功！',
				type: 'success',
				position: 'bottom-left',
				duration: 2000,
				onClose: function () {
					router.replace('/seller/teambuy/list')
				}
			})
		}, loading)
	}
	else {
		teambuyAdd(Object.assign(teambuy, { specs: rules, goods_id: goods.value.goods_id }), (data) => {
			ElNotification({
				title: '提示',
				message: '拼团商品添加成功！',
				type: 'success',
				position: 'bottom-left',
				duration: 2000,
				onClose: function () {
					router.replace('/seller/teambuy/list')
				}
			})
		}, loading)
	}
}

const dialogClose = (value) => {
	dialog.visible = false
	dialog.selected = value || []

	if (dialog.selected.length > 0) {
		query()
	}
}

function query() {
	goodsRead({ goods_id: dialog.selected[dialog.selected.length - 1] }, (data) => {
		goods.value = data
		goodsSpecs({ goods_id: data.goods_id }, (specs) => {

			for (let index in specs.list) {
				let item = specs.list[index]
				gallery.list[index] = {
					specification: item.spec_1 + ' ' + item.spec_2,
					price: item.price,
					stock: item.stock,
					image: item.image,
					spec_id: item.spec_id
				}

				//  编辑状态下，数据初始化
				let rules = teambuy.specs
				if (rules && rules[item.spec_id]) {
					gallery.list[index].discount = rules[item.spec_id].discount
				}
			}
		})
	})
}

</script>
<style scoped>
.el-form .el-form-item {
	margin-right: 40%;
}

.el-form .el-input {
	width: 220px;
}

.el-form .tips {
	width: 240px;
	background-color: rgb(235, 248, 252);
}
</style>