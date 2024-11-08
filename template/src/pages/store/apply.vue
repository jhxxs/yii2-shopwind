<template>
	<myhead></myhead>
	<div class="main w pt10">
		<div class="round-edge pd10 bgf">
			<h3 class="center mt20">商家入驻申请</h3>
			<div class="apply">
				<div v-if="form.apply_remark && form.state == 3" class="center pd10 mb20 remark l-h17">
					您的店铺未通过审核，{{ form.apply_remark }}</div>
				<el-form>
					<el-form-item label="主体：" :label-width="250">
						<el-radio-group v-model="form.stype" class="ml20">
							<el-radio label="personal" :disabled="form.store_id > 0" size="large" border
								style="padding:0 60px">个人</el-radio>
							<el-radio label="company" :disabled="form.store_id > 0" size="large" border
								style="padding:0 60px">企业</el-radio>
						</el-radio-group>
					</el-form-item>
					<el-form-item label=" " :label-width="250">
						<p class="f-gray f-12 ml20">注：主体类型设置后无法更改</p>
					</el-form-item>
					<el-form-item v-if="sgrades.length > 0" label="店铺等级：" :label-width="250">
						<el-select v-model="form.sgrade" @change="change" class="ml20">
							<el-option v-for="item in sgrades" :label="item.grade_name" :value="item.grade_id" />
						</el-select>
						<p class="ml20 mt10 f-gray f-13" style="width:calc(100% - 20px)">
							<span class="mr20">发布商品数限制：{{ sgrade.goods_limit || '不限制' }}件</span>
							<span class="mr20">图片空间量限制：{{ sgrade.space_limit }}MB</span>
							<span>{{ sgrade.description }}</span>
						</p>
					</el-form-item>
					<el-form-item label="所属分类：" :label-width="250">
						<p class="ml20"></p>
						<multiselector api="scategory/list" idField="cate_id" nameField="cate_name"
							parentField="parent_id" :original="form.category"
							@callback="(data) => { callback(data, 'cate_id') }">
						</multiselector>
					</el-form-item>
					<el-form-item label="店铺名称：" :label-width="250">
						<el-input v-model="form.store_name" class="ml20" />
					</el-form-item>
					<el-form-item label="主体名称：" :label-width="250">
						<el-input v-model="form.owner" class="ml20"
							:placeholder="form.stype == 'personal' ? '申请者姓名' : '企事业单位名称'" />
					</el-form-item>
					<el-form-item v-if="form.stype == 'personal'" label="身份证号：" :label-width="250">
						<el-input v-model="form.identity_card" class="ml20" />
					</el-form-item>
					<el-form-item v-if="form.stype == 'company'" label="统一社会信用代码：" :label-width="250">
						<el-input v-model="form.identity_card" class="ml20" />
					</el-form-item>
					<el-form-item v-if="form.stype == 'company'" label="工商营业执照：" :label-width="250">
						<el-upload action="#" :show-file-list="false" :auto-upload="false"
							:on-change="(file) => { fileUpload(file, 'business_license') }" class="ml20 relative">
							<p v-if="form.business_license">
								<img :src="form.business_license + '?t=' + Math.random()" />
								<el-icon @click.stop="remove('business_license')" class="absolute remove"
									color="#ffffff">
									<close-bold />
								</el-icon>
							</p>
							<el-icon v-else class="add" :size="20">
								<plus />
							</el-icon>
							<p class="f-gray f-13 ml10 l-h17">请上传企业营业执照扫描件</p>
						</el-upload>
					</el-form-item>
					<el-form-item label="身份证件：" :label-width="250">
						<el-upload action="#" :show-file-list="false" :auto-upload="false"
							:on-change="(file) => { fileUpload(file, 'identity_front') }" class="ml20 relative">
							<p v-if="form.identity_front">
								<img :src="form.identity_front + '?t=' + Math.random()" />
								<el-icon @click.stop="remove('identity_front')" class="absolute remove" color="#ffffff">
									<close-bold />
								</el-icon>
							</p>
							<el-icon v-else class="add" :size="20">
								<plus />
							</el-icon>
							<p class="f-gray f-13 ml10 l-h17">请上传本人（法人）<br />的身份证件（正面）</p>
						</el-upload>

						<el-upload action="#" :show-file-list="false" :auto-upload="false"
							:on-change="(file) => { fileUpload(file, 'identity_back') }" class="ml20 relative">
							<p v-if="form.identity_back">
								<img :src="form.identity_back + '?t=' + Math.random()" />
								<el-icon @click.stop="remove('identity_back')" class="absolute remove" color="#ffffff">
									<close-bold />
								</el-icon>
							</p>
							<el-icon v-else class="add" :size="20">
								<plus />
							</el-icon>
							<p class="f-gray f-13 ml10 l-h17">请上传本人（法人）<br />的身份证件（反面）</p>
						</el-upload>

					</el-form-item>
					<el-form-item label="联系电话：" :label-width="250">
						<el-input v-model="form.tel" class="ml20" />
					</el-form-item>

					<el-form-item label="所在地区：" :label-width="250">
						<p class="ml20"></p>
						<multiselector api="region/list" idField="region_id" nameField="name"
							parentField="parent_id" :original="[form.province, form.city, form.district]"
							@callback="(data) => { callback(data, 'region_id') }">
						</multiselector>
					</el-form-item>
					<el-form-item label="详细地址：" :label-width="250">
						<el-input v-model="form.address" class="ml20" />
					</el-form-item>

					<el-form-item label=" " :label-width="250" class="pt10">
						<el-button @click="submit" type="primary" class="mt20 ml20" :loading="loading"> 提交审核
						</el-button>
					</el-form-item>
				</el-form>
			</div>
		</div>
	</div>
	<myfoot></myfoot>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { storeGradeList, storeAdd, storeRead, storePrivacy } from '@/api/store.js'
import { siteRead } from '@/api/site.js'
import { uploadFile } from '@/api/upload.js'
import { redirect } from '@/common/util.js'

import multiselector from '@/components/selector/multiselector.vue'
import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'

const loading = ref(false)
const visitor = ref({})
const sgrades = ref([])
const sgrade = ref({})
const form = ref({ stype: 'personal' })

onMounted(() => {

	storeGradeList(null, (data) => {
		sgrades.value = data.list || []
		if (data.list.length > 0) {
			sgrade.value = data.list[0]
			form.value.sgrade = sgrade.value.grade_id
		}
	})

	visitor.value = JSON.parse(localStorage.getItem('visitor'))
	verifyApply({ store_id: visitor.value.userid })
})

const submit = () => {

	storeAdd(form.value, (data) => {
		ElMessage.success(data.state == 1 ? '您的店铺已开通' : '您的开店申请已提交，待平台审核后开通！')
		verifyApply(data)
	}, loading)
}

const callback = (data, field) => {
	form.value[field] = data.id
}
const fileUpload = (file, field) => {
	uploadFile(file.raw, { store_id: visitor.value.userid, folder: 'identity/' }, (data) => {
		form.value[field] = data.fileUrl
	})
}
const remove = (field) => {
	form.value[field] = ''
}
const change = (value) => {
	for (let i = 0; i < sgrades.value.length; i++) {
		if (sgrades.value[i].grade_id == value) sgrade.value = sgrades.value[i]
	}
}

const verifyJoin = () => {
	siteRead(null, (data) => {
		if (!data || !data.store_allow) {
			redirect('/message/result', 'warn', '商家入驻', '暂不开放商家入驻')
		}
	})
}

function verifyApply(params) {
	storeRead(params, (store) => {

		// 没有店铺
		if (!store) {
			verifyJoin()
		} else {
			
			// 店铺审核中
			if (store.state == 0) {
				redirect('/message/result', 'clock', '店铺审核中', store.apply_remark)
			} else if (store.state == 1) {
				visitor.value.store_id = store.store_id
				localStorage.setItem('visitor', JSON.stringify(visitor.value))
				redirect('/message/result?redirect=/seller/index', 'success', '店铺已开通', '恭喜！您的店铺已开通，您可以发布商品及售卖了')
			}
			// 店铺被关闭
			else if (store.state == 2) {
				redirect('/message/result', 'fail', '店铺已被关闭', store.close_reason)
			}
			// 平台审核不通过，需要修改
			else if (store.state == 3) {

				form.value = store
				storePrivacy(null, (privacy) => {
					let fields = ['owner', 'identity_card', 'identity_front', 'identity_back', 'business_license']
					for (let index in fields) {
						form.value[fields[index]] = privacy[fields[index]]
					}
				})
			}
		}
	})
}


</script>

<style scoped>
.apply {
	margin: 40px 0px;
}

.portrait img {
	border-radius: 100%;
}

.el-input {
	width: 300px;
}

:deep() .el-form .el-radio__inner {
	display: none;
}

.el-radio.is-bordered.is-checked {
	border-color: var(--el-color-primary);

}

:deep() .el-radio__input.is-checked+.el-radio__label {
	color: var(--el-color-primary);
}

.apply img,
.apply .add {
	border: 1px #ddd dotted;
	width: 100px;
	height: 100px;
	line-height: 100px;
	border-radius: 4px;
}

.apply .remove {
	background-color: #000;
	opacity: 0.7;
	left: -5px;
	top: -10px;
	border-radius: 100%;
	padding: 3px;
}

.apply .remark {
	width: 80%;
	margin: 0 auto 30px auto;
	background-color: #edf6ff;
	border-radius: 4px;
}
</style>
