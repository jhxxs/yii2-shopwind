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
							<el-breadcrumb-item>商品</el-breadcrumb-item>
							<el-breadcrumb-item>商品发布</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				</div>
				<div class="round-edge pd10 bgf mt20">
					<div class="pl10 pr10 pt10">
						<el-form class="pl10 pr10">
							<el-form-item label="">
								<strong class="f-16">基本信息</strong>
							</el-form-item>
							<el-form-item label="商品类型" :label-width="100">
								<div class="uni-flex uni-row gtypes flex-wrap">
									<div @click="goods.type = 'material'"
										:class="['item pointer pl10 pt5 pb5 pr10 ml20 relative', goods.type == 'material' ? 'selected' : '']">
										<p class="ml10 bold">实物商品</p>
										<p class="f-13 f-gray">（物流发货）</p>
										<el-icon v-if="goods.type == 'material'" class="ico absolute"><Select /></el-icon>
									</div>
									<div @click="goods.type = 'service'"
										:class="['item pointer pl10 pt5 pb5 pr10 ml20 relative', goods.type == 'service' ? 'selected' : '']">
										<p class="ml10 bold">服务商品</p>
										<p class="f-13 f-gray">（线下核销）</p>
										<el-icon v-if="goods.type == 'service'" class="ico absolute"><Select /></el-icon>
									</div>
									<div @click="goods.type = 'virtual'"
										:class="['item pointer pl10 pt5 pb5 pr10 ml20 relative', goods.type == 'virtual' ? 'selected' : '']">
										<p class="ml10 bold">虚拟商品</p>
										<p class="f-13 f-gray">（虚拟发货）</p>
										<el-icon v-if="goods.type == 'virtual'" class="ico absolute"><Select /></el-icon>
									</div>
								</div>
							</el-form-item>
							<el-form-item label="商品名称" :label-width="100">
								<el-input v-model="goods.goods_name" style="width:640px;" maxlength="80" show-word-limit />
							</el-form-item>
							<el-form-item label="商品副标题" :label-width="100">
								<div class="uni-flex uni-column">
									<el-input v-model="goods.tags" style="width:300px;" maxlength="40" show-word-limit />
									<span class="f-gray f-13">商品卖点，展示在移动端标题下方</span>
								</div>
							</el-form-item>
							<el-form-item label="商品类目" :label-width="100" v-loading="loading">
								<div v-if="!route.params.id || !goods.loading" class="uni-flex uni-row">
									<multiselector api="category/list" idField="cate_id" nameField="cate_name"
										parentField="parent_id" @callback="callback" :original="goods.categorys">
									</multiselector>
								</div>
							</el-form-item>
							<el-form-item v-if="attributes.length > 0" label="类目属性" :label-width="100">
								<div class="attributes">
									<div v-for="attribute in attributes" class="uni-flex uni-row flex-wrap mb5 f-13">
										<p class="mr10 pr10">{{ attribute.name }}</p>
										<el-select v-if="attribute.ptype == 'select'"
											v-model="goods.attributes[attribute.pid]" placeholder="请选择" clearable>
											<el-option v-for="item in attribute.values" :label="item.value"
												:value="item.vid" />
										</el-select>
										<el-radio-group v-if="attribute.ptype == 'radio'"
											v-model="goods.attributes[attribute.pid]">
											<el-radio v-for="item in attribute.values" :label="item.vid">
												{{ item.value }}</el-radio>
										</el-radio-group>
										<el-checkbox-group v-if="attribute.ptype == 'checkbox'"
											v-model="goods.attributes[attribute.pid]">
											<el-checkbox v-for="item in attribute.values" :label="item.vid">
												{{ item.value }}</el-checkbox>
										</el-checkbox-group>
									</div>
								</div>
							</el-form-item>

							<el-form-item label="品牌" :label-width="100">
								<el-select v-model="goods.brand" placeholder="请选择" clearable>
									<el-option v-for="item in brands" :label="item.name" :value="item.name" />
								</el-select>
							</el-form-item>

							<el-form-item label="商品规格" :label-width="100">
								<el-radio-group v-model="goods.multispec">
									<el-radio :label="false">单规格</el-radio>
									<el-radio :label="true">多规格</el-radio>
								</el-radio-group>
							</el-form-item>
							<div v-if="goods.multispec">
								<el-form-item label=" " :label-width="100">
									<div v-for="quantity of gallery.specs.length" class="specitem mb20">
										<div class="uni-flex uni-row pd10 title flex-middle width-between">
											<div>
												<span>规格名</span>
												<el-input v-model="gallery.specs[quantity - 1].name"
													class="normal-width ml10"
													@change="(value) => { changeClick(value, quantity - 1, index) }"
													clearable />
												<el-checkbox v-model="goods.hasspecimg" v-if="quantity == 1"
													class="pl10 pr10">规格图片</el-checkbox>
											</div>
											<el-icon @click="changeSpec('del', quantity - 1)" v-if="quantity > 1"
												class="f-gray f-18 pointer">
												<CircleCloseFilled />
											</el-icon>
										</div>
										<div class="uni-flex uni-row flex-wrap content pd10" style="padding-bottom:0px;">
											<span>规格值</span>
											<p v-for="(item, index) in gallery.specs[quantity - 1].items" :key="index"
												:class="['relative mb20', (index > 0 && index % 3 == 0) ? 'ml' : '']">
												<el-input v-model="item.value"
													@change="(value) => { changeClick(value, quantity - 1, index) }"
													class="normal-width ml10" clearable />
												<el-icon v-if="gallery.specs[quantity - 1].items.length > 1"
													@click="changeSpecItem('del', quantity - 1, index)"
													class="f-gray f-18 pointer absolute">
													<CircleCloseFilled />
												</el-icon>
												<el-upload v-if="quantity == 1 && goods.hasspecimg" action="#"
													:show-file-list="false" :auto-upload="false"
													:on-change="(file) => { fileUpload(file, 'spec_images', index) }"
													class="relative slider mt10 ml10">
													<img v-if="item.image" :src="item.image" />
													<el-icon v-else class="add f-gray" :size="24">
														<plus />
													</el-icon>
												</el-upload>
											</p>
											<label @click="changeSpecItem('add', quantity - 1, index + 1)"
												class="pl10 pr10 f-blue pointer mb20">添加规格值</label>
										</div>
									</div>
									<div class="addspec">
										<el-button @click="changeSpec('add', gallery.specs.length)"
											:disabled="gallery.specs.length > 1" type="primary" plain>添加规格</el-button>
										<label class="f-gray ml10">最多添加2个规格</label>
									</div>

								</el-form-item>
								<el-form-item v-if="gallery.list.length > 0" label="规格明细" :label-width="100">
									<el-table ref="multipleTableRef" :data="gallery.list" :border="true" :stripe="false"
										max-height="367" size="small" @selection-change="selectionChange"
										:row-class-name="tableRowClassName">
										<el-table-column type="selection" />
										<el-table-column v-for="(spec, key) in gallery.specs" :key="key" :label="spec.name">
											<template #default="scope">
												{{ scope.row['spec_' + (key + 1)] }}
											</template>
										</el-table-column>
										<el-table-column label="划线价" width="140">
											<template #default="scope">
												<el-input v-model="scope.row.mkprice" class="small" clearable />
											</template>
										</el-table-column>
										<el-table-column label="售价" width="140">
											<template #default="scope">
												<el-input v-model="scope.row.price" class="small" clearable />
											</template>
										</el-table-column>
										<el-table-column prop="stock" label="库存" width="140">
											<template #default="scope">
												<el-input v-model="scope.row.stock" class="small" clearable />
											</template>
										</el-table-column>
									</el-table>
									<div v-if="popover.selected.length > 0" class=" pl10 pr10"
										style="border:1px #eee solid;border-top:0;width:100%;">
										<span class="mr20 f-gray f-12">批量设置</span>
										<el-popover v-for="item in ['mkprice', 'price', 'stock']"
											v-model:visible="popover[item]" placement="top" :width="160">
											<p class="center">
												<el-input v-model="popover.value" size="small" style="width:110px;"
													clearable />
											</p>
											<div class="center mt10">
												<span class="f-gray f-12 mr20 pointer"
													@click="popover[item] = false">取消</span>
												<el-button size="small" type="primary" @click="popoverClick(item)">确定
												</el-button>
											</div>
											<template #reference>
												<span v-if="item == 'mkprice'" @click="popover[item] = true"
													class="mr20 pointer f-12 f-red">划线价</span>
												<span v-if="item == 'price'" @click="popover[item] = true"
													class="mr20 pointer f-12 f-red">售价</span>
												<span v-if="item == 'stock'" @click="popover[item] = true"
													class="mr20 pointer f-12 f-red">库存</span>
											</template>
										</el-popover>
									</div>
								</el-form-item>
							</div>
							<div v-else>
								<el-form-item label="划线价" :label-width="100">
									<el-input v-model="goods.mkprice" class="small" clearable />
								</el-form-item>
								<el-form-item label="售价" :label-width="100">
									<el-input v-model="goods.price" class="small" clearable />
								</el-form-item>
								<el-form-item label="库存" :label-width="100">
									<el-input v-model="goods.stock" class="small" clearable />
								</el-form-item>
							</div>

							<el-form-item label="">
								<strong class="f-16">主图及描述</strong>
							</el-form-item>
							<el-form-item label="商品主图" :label-width="100">
								<div class="uni-flex uni-row flex-wrap">
									<el-upload v-for="(item, index) in goods.goods_images" action="#"
										:show-file-list="false" :auto-upload="false"
										:on-change="(file) => { fileUpload(file, 'goods_images', index) }"
										class="relative slider mr20 mb10">
										<img :src="item + '?t=' + Math.random()" />
										<el-icon @click.stop="fileRemove('goods_images', index)" class="absolute remove"
											color="#ffffff">
											<close-bold />
										</el-icon>
									</el-upload>
									<el-upload v-if="goods.goods_images.length < 9" action="#" :show-file-list="false"
										:auto-upload="false"
										:on-change="(file) => { fileUpload(file, 'goods_images', goods.goods_images.length) }"
										class="relative slider mb10">
										<el-icon class="add f-gray" :size="24">
											<plus />
										</el-icon>
									</el-upload>
								</div>
								<div class="f-13 f-gray" style="width:100%;">
									尺寸建议800PX*800PX，支持JPG/JPEG/GIF/PNG格式，大小不超过2M</div>
							</el-form-item>
							<el-form-item label="商品描述" :label-width="100">
								<div class="w-e-text-container border-radius">
									<Toolbar :editor="editorRef" :defaultConfig="toolbarConfig" mode="simple" />
									<Editor v-model="goods.description" :defaultConfig="editorConfig" mode="simple"
										@onCreated="handleCreated" @onChange="onChange" style="height: 500px;" />
								</div>
							</el-form-item>
							<el-form-item v-if="scategory.length > 0" label="本店分类" :label-width="100">
								<el-tree ref="treeRef" :data="scategory" :default-checked-keys="goods.scategory"
									node-key="id" :props="{ label: 'value' }" show-checkbox empty-text=""
									class="pd10 width-surplus border-radius">
								</el-tree>
							</el-form-item>
							<el-form-item label="是否添加视频" :label-width="100">
								<el-switch v-model="goods.hasvideo" />
								<p v-if="goods.hasvideo" class="f-gray ml10 f-13 tips">
									添加视频后，将在移动端详情页轮播图第一屏显示视频
								</p>
							</el-form-item>
							<el-form-item v-if="goods.hasvideo" label=" " :label-width="100">
								<el-upload action="#" :show-file-list="false" :auto-upload="false"
									:on-change="(file) => { fileUpload(file, 'video') }" class="relative slider mr20 mb10">
									<p v-if="goods.video">
										<video :src="goods.video" :autoplay="false" />
										<el-icon @click.stop="fileRemove('video')" class="absolute remove" color="#ffffff">
											<close-bold />
										</el-icon>
									</p>
									<el-icon v-else class="add f-gray" :size="24">
										<plus />
									</el-icon>

								</el-upload>
								<p class="l-h17 f-13 f-c55 mb10">仅支持MP4格式，视<br />频大小不超过3MB</p>
							</el-form-item>
							<el-form-item label="是否添加长图" :label-width="100">
								<el-switch v-model="goods.haslongimg" />
								<p v-if="goods.haslongimg" class="f-gray ml10 f-13 tips">
									添加长图后，将在移动端商品瀑布流组件显示长图
								</p>
							</el-form-item>
							<el-form-item v-if="goods.haslongimg" label=" " :label-width="100">
								<el-upload action="#" :show-file-list="false" :auto-upload="false"
									:on-change="(file) => { fileUpload(file, 'long_image') }"
									class="relative slider mr20 mb10">

									<p v-if="goods.long_image">
										<img :src="goods.long_image" />
										<el-icon @click.stop="fileRemove('long_image')" class="absolute remove"
											color="#ffffff">
											<close-bold />
										</el-icon>
									</p>
									<el-icon v-else class="add f-gray" :size="24">
										<plus />
									</el-icon>

								</el-upload>
								<p class="l-h17 f-13 f-c55 mb10">宽高建议为800PX*1200PX<br />JPG格式，大小不超过2MB</p>
							</el-form-item>

							<el-form-item v-if="goods.type == 'material'" label="">
								<strong class="f-16">物流及配送</strong>
							</el-form-item>
							<el-form-item v-if="goods.type == 'material'" label="运费模板" :label-width="100">
								<div v-if="deliverys.list.length > 0" class="uni-flex uni-column">
									<el-select v-model="goods.dt_id" @change="deliveryChange">
										<el-option v-for="item in deliverys.list" :label="item.name"
											:value="item.template_id" />
									</el-select>
									<view class="round-edge pd10 mt10 l-h20 f-13 bgp">
										<view class="pt5 pb5 f-14 bold">
											快递配送</view>
										<view class="uni-flex uni-row mt5">
											<text class="f-gray mr10">发货时间</text>
											<text class="ml10 width-surplus">48小时内发货</text>
										</view>
										<view class="uni-flex uni-row">
											<text class="f-gray mr10">费用规则</text>
											<text class="ml10 width-surplus">
												{{ deliverys.selected.start_standards || 1 }}件内
												{{ deliverys.selected.start_fees || 10 }}元，每增加
												{{ deliverys.selected.add_standards || 1 }}件加
												{{ deliverys.selected.add_fees || 0 }}元
											</text>
										</view>
									</view>
								</div>
								<div v-else>
									<p class="f-gray">
										<span>暂无运费模板</span>
										<router-link to="/seller/delivery/build" class="f-blue rlink ml10">设置
										</router-link>
									</p>
									<p class="pd10 bgp round-edge f-13">
										首件运费
										<el-input v-model="form.delivery.start_fees" class="small pl5 pr5" />元，
										每增加1件增加
										<el-input v-model="form.delivery.add_fees" class="small pl5 pr5" />元
									</p>
								</div>
							</el-form-item>
							<el-form-item label="">
								<strong class="f-16">优惠设置</strong>
							</el-form-item>
							<el-form-item v-if="integral.enabled == 1" label="积分抵扣" :label-width="100">
								<el-input-number v-model="goods.integral.exchange_integral" :min="0"
									controls-position="right" clearable />
								<p class="f-gray ml10 f-13">设置允许买家最多可使用多少积分抵扣价款</p>
							</el-form-item>
							<el-form-item label="手机专享" :label-width="100">
								<el-switch v-model="goods.exclusive.status" :active-value="1" :inactive-value="0"
									:disabled="exclusive.status != 1" />
								<p v-if="goods.exclusive.status == 1" class="f-gray ml10 f-13 tips">
									通过手机下单，
									<span v-if="exclusive.discount > 0">可享{{ exclusive.discount }}折</span>
									<span v-else>每件优惠{{ exclusive.decrease }}元</span>优惠（默认）
								</p>
							</el-form-item>
							<el-form-item v-if="goods.exclusive.status == 1" label=" " :label-width="100">
								<div class="uni-flex uni-row f-13">
									享
									<el-input v-model="goods.exclusive.discount" class="small ml5 mr5" clearable /> 折，或减
									<el-input v-model="goods.exclusive.decrease" class="small ml5 mr5" clearable />
									元。<span class="f-gray">如果留空，则执行默认优惠。折扣和减价仅需设置一项</span>
								</div>
							</el-form-item>

							<el-form-item label="">
								<strong class="f-16">其他设置</strong>
							</el-form-item>
							<el-form-item label="是否推荐" :label-width="100">
								<el-switch v-model="goods.recommended" :active-value="1" :inactive-value="0" />
								<span class="f-gray f-13 ml10">将有机会在首页显示</span>
							</el-form-item>
							<el-form-item label="是否新品" :label-width="100">
								<el-switch v-model="goods.isnew" :active-value="1" :inactive-value="0" />
								<span class="f-gray f-13 ml10">在移动端显示新款标</span>
							</el-form-item>
							<el-form-item label="是否上架" :label-width="100">
								<el-switch v-model="goods.if_show" :active-value="1" :inactive-value="0" />
								<span class="f-gray f-13 ml10">发布后立即上架销售</span>
							</el-form-item>
							<el-form-item :label-width="100">
								<el-button @click="submit" type="primary" class="mt10"><text
										class="pl10 pr10">提交发布</text></el-button>
							</el-form-item>

						</el-form>
					</div>
				</div>
			</el-col>
		</el-row>
	</div>

	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onBeforeUnmount, onMounted, shallowRef } from 'vue'
import { ElMessage, ElNotification, ElTree } from 'element-plus'
import { useRoute } from 'vue-router'
import { DomEditor } from '@wangeditor/editor'
import { Editor, Toolbar } from '@wangeditor/editor-for-vue'
import { redirect, isEmpty } from '@/common/util.js'
import { goodsRead, goodsAdd, goodsUpdate, goodsImages, goodsSpecs } from '@/api/goods.js'
import { brandList } from '@/api/brand.js'
import { uploadFile, uploadAdd } from '@/api/upload.js'
import { templateList } from '@/api/delivery.js'
import { exclusiveRead } from '@/api/exclusive.js'
import { integralSetting } from '@/api/integral.js'
import { categoryAttributes, categoryTree } from '@/api/category.js'

import multiselector from '@/components/selector/multiselector.vue'
import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'
import '@wangeditor/editor/dist/css/style.css'


// 编辑器实例，必须用 shallowRef
const editorRef = shallowRef()

// 工具栏配置
const toolbarConfig = {
	excludeKeys: ['|', 'fullScreen', "header3", "header4", "header5"],
	//insertKeys: { index: 0, keys: ['fontSize', 'fontFamily'] }
}
const editorConfig = {
	placeholder: '请输入内容...',
	MENU_CONF: {
		'uploadImage': {
			async customUpload(file, insertFn) {
				uploadAdd(file, { item_id: goods.goods_id, folder: 'goods/' }, (data) => {
					insertFn(data.fileUrl)
				})
			}

			// customBrowseAndUpload(insertFn) {              // JS 语法
			// 	// 自己选择文件
			// 	// 自己上传文件，并得到图片 url alt href
			// 	// 最后插入图片
			// 	insertFn(url)
			// }
		}
	}
	//,modalAppendToBody: true
	//,maxLength:1200,
	// minLength:200,
}

// 组件销毁时，也及时销毁编辑器
onBeforeUnmount(() => {
	const editor = editorRef.value
	if (editor == null) return
	editor.destroy()
})

const handleCreated = (editor) => {
	editorRef.value = editor // 记录 editor 实例，重要！
	//console.log(editor.getAllMenuKeys())
}

const onChange = (editor) => {
	const toolbar = DomEditor.getToolbar(editor)
	//console.log("工具栏配置", toolbar.getConfig().toolbarKeys); // 工具栏配置
}

const treeRef = ref(ElTree)
const route = useRoute()
const loading = ref(true)
const visitor = ref({})
const goods = reactive({ loading: true, type: 'material', attributes: {}, hasvideo: true, exclusive: { status: 1 }, if_show: 1, categorys: [], scategory: [], multispec: false, integral: { exchange_integral: 0 }, goods_images: [], desc_images: [] })
const gallery = reactive({ list: [], specs: [{ name: '', items: [{ value: '' }] }] })
const deliverys = reactive({ list: [], selected: {} })
const brands = ref([])
const exclusive = ref({})
const integral = ref({ enabled: 1 })
const scategory = ref([])
const attributes = ref([])
const form = reactive({ delivery: { start_fees: 10, add_fees: 8 } })
const popover = reactive({ mkprice: false, price: false, stock: false, value: 100, selected: [] })

onMounted(() => {
	visitor.value = JSON.parse(localStorage.getItem('visitor'))

	if (route.params.id > 0) {
		goodsRead({ goods_id: route.params.id, querydesc: true }, (data) => {
			if (data) {
				goodsImages({ goods_id: data.goods_id }, (res) => {
					res.list.forEach((image) => {
						goods.goods_images.push(image.thumbnail)
					})
				})
				data.category.forEach((item) => {
					goods.categorys.push(item.cate_name)
				})
				Object.assign(goods, data, { haslongimg: data.long_image ? true : false })

				exclusiveRead({ goods_id: data.goods_id }, (data) => {
					Object.assign(goods.exclusive, data)
				})

				if (goods.spec_qty > 0) {
					goods.multispec = true
					bindSpecs()
				}
			}
			goods.loading = false
		})
	}

	brandList({ page_size: 1000 }, (data) => {
		brands.value = data.list
	})
	templateList({ store_id: visitor.value.userid }, (data) => {
		if (data && data.list.length > 0) {
			deliverys.list = data.list
			deliverys.selected = data.list[0].area_fee.express.default_fee
			if (!goods.dt_id) goods.dt_id = data.list[0].template_id
		}
	})
	exclusiveRead(null, (data) => {
		exclusive.value = data
		Object.assign(goods.exclusive, data)
	})
	integralSetting(null, (data) => {
		integral.value = data
	})

	categoryTree({ store_id: visitor.value.store_id }, (data) => {
		scategory.value = data
	})

})

const callback = (value) => {
	loading.value = false
	goods.cate_id = value.id

	categoryAttributes({ cate_id: value.id, goods_id: route.params.id }, (data) => {
		attributes.value = data.list || []

		// 设置选中
		goods.attributes = {}
		attributes.value.forEach((attr) => {
			attr.values.forEach((item) => {
				if (item.selected) {
					if (attr.ptype == 'checkbox') {
						if (isEmpty(goods.attributes[attr.pid])) goods.attributes[attr.pid] = [item.vid]
						else goods.attributes[attr.pid].push(item.vid)
					} else goods.attributes[attr.pid] = item.vid
				}
			})
		})
	})
}

const changeClick = (value, quantity, index) => {
	if (value == '') {
		if (index) {
			changeSpecItem('del', quantity, index)
		} else {
			//changeSpec('del', quantity)
		}
	}

	let items = gallery.specs[quantity].items

	// 重复值检测
	for (let i in items) {
		if (items[i].value == value && index != i) {
			items[index].value = ''
			ElMessage.warning('已有相同的属性值：' + value)
			return false
		}
	}
	bindTable()
}
const changeSpec = (target, quantity) => {
	if (target == 'add') {
		gallery.specs.push({ name: '', items: [{ value: '' }] })
	} else {
		gallery.specs.splice(quantity, 1)
		bindTable()
	}
}
const changeSpecItem = (target, quantity, index) => {
	let items = gallery.specs[quantity].items

	if (target == 'add') {
		items.push({ value: '' })
	} else {
		let list = []
		gallery.list.forEach((item) => {
			if (item['spec_' + (quantity + 1)] != items[index].value) {
				list.push(item)
			}
		})
		gallery.list = list
		items.splice(index, 1)
	}
}
const fileUpload = (file, field, index) => {
	let params = { item_id: goods.goods_id, folder: 'goods/', store_id: visitor.value.userid }

	if (field == 'goods_images') {
		uploadAdd(file.raw, Object.assign(params, { thumbnail: true, belong: 2 }), (data) => {
			goods[field][parseInt(index)] = data.thumbnail
		})
	}
	else if (field == 'desc_images') {
		uploadAdd(file.raw, params, (data) => {
			goods[field][parseInt(index)] = data.fileUrl
		})
	} else if (field == 'spec_images') {
		uploadFile(file.raw, Object.assign(params, { thumbnail: true }), (data) => {
			gallery.specs[0].items[index].image = data.thumbnail
		})
	}
	else {
		uploadFile(file.raw, Object.assign(params, field == 'video' ? { archived: 2, folder: 'video/' } : {}), (data) => {
			if (!isNaN(parseInt(index)) && isFinite(index)) {
				goods[field][index] = data.fileUrl
			} else goods[field] = data.fileUrl
		})
	}
}
const fileRemove = (field, index) => {
	if (!isEmpty(goods[field][index])) {
		goods[field].splice(index, 1)
	} else goods[field] = ''
}
const deliveryChange = (value) => {
	goods.dt_id = value
	for (let i in deliverys.list) {
		if (deliverys.list[i].template_id == value) {
			deliverys.selected = deliverys.list[i].area_fee.express.default_fee
			break
		}
	}
}

const tableRowClassName = ({ row, rowIndex }) => {
	row.index = rowIndex
}
const selectionChange = (selection) => {
	popover.selected = []
	if (selection.length > 0) {
		selection.forEach((item) => {
			popover.selected.push(item.index)
		})
	}
}
const popoverClick = (field) => {
	popover[field] = false
	if (popover.selected.length > 0) {
		gallery.list.forEach((item) => {
			if (popover.selected.indexOf(item.index) > -1) {
				item[field] = popover.value
			}
		})
	}
}

const submit = () => {
	form.spec_qty = 0
	form.integral_exchange = goods.integral.exchange_integral
	form.video = goods.hasvideo ? goods.video : ''
	form.haslongimg = goods.haslongimg ? goods.long_image : ''

	if (scategory.value.length > 0) {
		form.scate_id = treeRef.value ? treeRef.value.getCheckedKeys(false) : []
	}

	if (!goods.multispec) gallery.list = []
	else if (gallery.specs.length > 0) {

		for (let i = 0; i < gallery.specs.length; i++) {
			let item = gallery.specs[i]
			if (item.name) {
				form.spec_qty++
				form['spec_name_' + (i + 1)] = item.name

				// 规格名是否有重复
				if (i > 0) {
					if (form['spec_name_1'] == item.name) {
						return ElMessage.warning('规格名不能重复')
					}
				}
			} else return ElMessage.warning('规格名不能为空')
		}

		form.specs = gallery.list || []
		gallery.specs[0].items.forEach((find) => {
			form.specs.forEach((item) => {
				if (item.spec_1 == find.value) {
					item.image = (find.image && goods.hasspecimg) ? find.image : ''
				}

				// 规格名为空，规格值也置空
				for (let i = 1; i <= 2; i++) {
					if (!form['spec_name_' + i] || form['spec_name_' + i] == '') {
						item['spec_' + i] = ''
					}
				}
			})
		})
	}

	// 处理字段
	['price', 'stock', 'mkprice', 'type', 'goods_name', 'goods_images', 'desc_images', 'tags', 'cate_id', 'dt_id', 'brand', 'default_image', 'isnew', 'recommended', 'if_show', 'description', 'attributes', 'exclusive'].forEach((field) => {
		//if (!isEmpty(goods[field])) { // 与不传不更新规则冲突
		form[field] = goods[field]
		//}
	})

	if (goods.goods_id > 0) {
		goodsUpdate(Object.assign(form, { goods_id: goods.goods_id }), (data) => {
			ElNotification({
				title: '提示',
				message: '商品信息更新成功！',
				type: 'success',
				position: 'bottom-left',
				duration: 2000,
				onClose: function () {
					redirect('/seller/goods/list')
				}
			})
		})
	} else {
		goodsAdd(form, (data) => {
			ElNotification({
				title: '提示',
				message: '商品添加成功！',
				type: 'success',
				position: 'bottom-left',
				duration: 2000,
				onClose: function () {
					redirect('/seller/goods/list')
				}
			})
		})
	}
}

function bindSpecs() {
	goodsSpecs({ goods_id: route.params.id }, (data) => {
		if (data.list.length > 0) {

			let values = [[], []]
			data.list.forEach((item, index) => {
				gallery.list.push({ spec_id: parseInt(item.spec_id), spec_1: item.spec_1, spec_2: item.spec_2, price: item.price, stock: item.stock, mkprice: item.mkprice, image: item.image })
				if (item.image) goods.hasspecimg = true

				for (let i = 0; i < 2; i++) {
					if (item['spec_' + (i + 1)] && item['spec_name_' + (i + 1)]) {
						if (index == 0) gallery.specs[i] = { name: '', items: [] } // 重置

						if (values[i].indexOf(item['spec_' + (i + 1)]) == -1) {
							values[i].push(item['spec_' + (i + 1)])
							gallery.specs[i].name = item['spec_name_' + (i + 1)]
							gallery.specs[i].items.push({ value: item['spec_' + (i + 1)], image: item.image })
						}
					}
				}
			})
		}
	})
}

function bindTable() {
	let table = []
	let spec = {}
	gallery.specs[0].items.forEach((each, index) => {
		spec = { spec_1: each.value, price: '', mkprice: '', stock: '' }
		if (!isEmpty(gallery.list[index])) {
			Object.assign(spec, gallery.list[index], { spec_1: each.value })
		}
		if (gallery.specs[1] && gallery.specs[1].items.length > 0) {
			gallery.specs[1].items.forEach((item) => {
				spec = { spec_1: each.value, spec_2: item.value }
				for (let i in gallery.list) {
					if (gallery.list[i].spec_1 == each.value && gallery.list[i].spec_2 == item.value) {
						Object.assign(spec, gallery.list[i])
						break
					}
				}
				table.push(spec)
			})
		} else table.push(spec)
	})
	gallery.list = table
}

</script>

<style scoped>
.el-table,
.el-form-item {
	font-size: 13px;
}

.el-form-item .small {
	width: 120px;
}

.el-form-item .normal-width {
	width: 200px;
}

.el-form-item .notice {
	border: 1px #f1f1f1 solid;
	background-color: #fafafa;
}

.slider img,
.slider video,
.slider .add {
	border: 1px #eee solid;
	width: 100px;
	height: 100px;
	line-height: 100px;
	border-radius: 4px;
	font-weight: lighter;
}

.slider .remove {
	background-color: #000;
	opacity: 0.7;
	right: -10px;
	top: -10px;
	border-radius: 100%;
	padding: 3px;
}

.specitem {
	width: 100%;
	border-radius: 4px;
	border: 1px #f3f5f7 solid;
}

.specitem .title {
	background-color: #f3f5f7;
}

.specitem .content .absolute {
	right: -6px;
	top: -12px;
}

.specitem .content .ml {
	margin-left: 42px;
}

.specitem .slider img,
.specitem .slider .add {
	width: 70px;
	height: 70px;
}

.attributes {
	background: #f3f5f7;
	padding: 10px;
	border-radius: 4px;
	width: calc(100% - 2px);
}

.attributes p {
	width: 60px;
	text-align: right;
}

.gtypes .item {
	border: 1px #BAD8FA solid;
	display: inline-block;
	border-radius: 4px;
	padding-right: 40px;
}

.gtypes .item:first-child {
	margin-left: 0;
}

.gtypes .item.selected {
	background-color: #fbf4ef;
	border-color: var(--el-color-danger);
}

.gtypes .item.selected .absolute {
	bottom: 4px;
	right: 4px;
	color: var(--el-color-danger);
}

.border-radius {
	border: 1px #f1f1f1 solid;
	border-radius: 4px;
}

:deep() .el-table__header-wrapper .el-table-column--selection .el-checkbox {
	vertical-align: middle;
}

:deep() .w-e-text-placeholder {
	top: 10px;
}</style>
