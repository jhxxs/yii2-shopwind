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
							<el-breadcrumb-item>物流</el-breadcrumb-item>
							<el-breadcrumb-item>运费模板</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<el-table :data="gallery" :border="false" :stripe="false" v-loading="loading">
						<el-table-column prop="name" label="名称" />
						<el-table-column fixed="right" label="操作" width="140" align="center">
							<template #default="scope">
								<el-button type="primary" size="small"
									@click="redirect('/seller/delivery/build/' + scope.row.template_id)" plain>编辑
								</el-button>
								<el-button type="warning" size="small" @click="deleteClick(scope.$index)" plain>删除
								</el-button>
							</template>
						</el-table-column>
					</el-table>
				</div>
			</el-col>
		</el-row>
	</div>
	<myfoot></myfoot>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ElMessageBox, ElMessage } from 'element-plus'
import { redirect } from '@/common/util.js'
import { templateList, deliveryDelete } from '@/api/delivery.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const loading = ref(false)
const gallery = ref([])

onMounted(() => {
	let visitor = JSON.parse(localStorage.getItem('visitor')) || {}

	templateList({ store_id: visitor.userid }, (data) => {
		gallery.value = data.list || []
	}, loading)
})

const deleteClick = (value) => {
	ElMessageBox.confirm('您确定要删除该运费模板吗？', '提示', {
		confirmButtonText: '确定',
		type: 'warning'
	}).then(() => {
		deliveryDelete({ id: gallery.value[value].template_id }, () => {
			ElMessage.success('删除成功！')
			gallery.value.splice(value, 1)
		})
	}).catch(() => { })
}

</script>