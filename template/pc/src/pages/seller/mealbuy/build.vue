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
							<el-breadcrumb-item>搭配购</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<h3 class="pd10 mb20">搭配购设置</h3>

					<el-form :inline="true" class="pd10">
						<el-form-item label="标题" :label-width="100">
							<el-input v-model="mealbuy.title" clearable />
						</el-form-item>
						<el-form-item label="选择商品" :label-width="100">
							<el-button @click="dialog.visible = true" type="primary" plain>选择商品</el-button>
						</el-form-item>
						<el-form-item label=" " :label-width="100" style="margin-right:0;">
							<p class="tips pl10 pr10 pt5 pb5 f-12 f-c60 flex-middle" style="width:100%">
								<el-icon :size="14" class="mr5">
									<WarningFilled />
								</el-icon> 搭配购商品建议2-10个
							</p>
							<el-table :data="mealbuy.items" :border="true" :stripe="true">
								<el-table-column label="商品信息">
									<template #default="scope">
										<div class="uni-flex uni-row">
											<p><img :src="scope.row.goods_image || scope.row.default_image" width="60"
													height="60" /></p>
											<p class="l-h20 width-surplus ml10">{{ scope.row.goods_name }}</p>
										</div>
									</template>
								</el-table-column>
								<el-table-column label="库存" width="100">
									<template #default="scope">
										{{ scope.row.stocks }}
									</template>
								</el-table-column>
								<el-table-column label="价格" width="100">
									<template #default="scope">
										{{ scope.row.price }}
									</template>
								</el-table-column>
							</el-table>
						</el-form-item>
						<el-form-item label="原总价" :label-width="100">
							<el-input :value="mealbuy.total.length > 0 ? mealbuy.total.join(' - ') : mealbuy.total"
								readonly />
						</el-form-item>
						<el-form-item label="搭配价" :label-width="100">
							<el-input v-model="mealbuy.price" clearable />
						</el-form-item>
						<el-form-item label="启用" :label-width="100">
							<el-switch v-model="mealbuy.status" @change="changeClick" :active-value="1"
								:inactive-value="0" />
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

	<selector title="选择商品" :visible="dialog.visible" @close="dialogClose" :selected="dialog.selected" :limit="10">
	</selector>

	<myfoot></myfoot>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ElMessage, ElNotification } from 'element-plus'
import { useRoute } from 'vue-router'
import { goodsList } from '@/api/goods.js'
import { redirect } from '@/common/util.js'
import { mealbuyRead, mealbuyAdd, mealbuyUpdate } from '@/api/mealbuy.js'

import selector from '@/components/dialog/goods/list.vue'
import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const route = useRoute()
const loading = ref(false)
const dialog = reactive({ visible: false, selected: [] })
const mealbuy = reactive({ title: '搭配优惠', items: [], total: [], status: 1 })

onMounted(() => {
	if (route.params.id > 0) {
		mealbuyRead({ meal_id: route.params.id }, (data) => {
			if (data) {
				Object.assign(mealbuy, data, { status: parseInt(data.status) })
				mealbuy.items.forEach((item) => {
					dialog.selected.push(parseInt(item.goods_id))
				})
			}
		})
	}
})

const changeClick = (value) => {
	mealbuy.status = value
}

const submit = () => {

	if (mealbuy.items.length < 1) {
		return ElMessage.warning('请选择商品')
	}

	let selected = []
	for (let index in mealbuy.items) {
		selected.push(mealbuy.items[index].goods_id)
	}

	if (mealbuy.meal_id > 0) {
		mealbuyUpdate(Object.assign(mealbuy, { selected: selected }), (data) => {
			ElNotification({
				title: '提示',
				message: '搭配购设置成功！',
				type: 'success',
				position: 'bottom-left',
				duration: 2000,
				onClose: function () {
					redirect('/seller/mealbuy/list')
				}
			})
		}, loading)
	}
	else {
		mealbuyAdd(Object.assign(mealbuy, { selected: selected }), (data) => {
			ElNotification({
				title: '提示',
				message: '搭配购商品添加成功！',
				type: 'success',
				position: 'bottom-left',
				duration: 2000,
				onClose: function () {
					redirect('/seller/mealbuy/list')
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
	goodsList({ items: (dialog.selected).join(','), queryspec: true, if_show: 1 }, (data) => {
		mealbuy.items = data.list

		let total = [0, 0]
		mealbuy.items.forEach((item) => {

			let prices = [item.specs[0].price, item.specs[0].price]
			item.specs.forEach((goods) => {
				if (prices[0] > goods.price) prices[0] = goods.price
				if (prices[1] < goods.price) prices[1] = goods.price
			})
			total[0] += Number(prices[0])
			total[1] += Number(prices[1])

			mealbuy.total = (total[0] == total[1]) ? total[0] : total
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