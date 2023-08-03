<template>
	<div class="main">
		<myhead :exclude="['category', 'searchbox', 'imagead']"></myhead>
		<div class="w chatbox">
			<el-row class="round-edge wraper">
				<el-col :span="5" class="panel">
					<div class="hd pl10 ml5 vertical-middle">
						<el-icon :size="18" class="mr5">
							<ChatRound />
						</el-icon>聊天列表({{ gallery.list.length }})
					</div>
					<el-scrollbar height="550px">
						<div v-for="(item) in gallery.list" class="uni-flex uni-column bgf item pointer">
							<div v-if="visitor.userid == item.store_id" @click="redirect('/webim/chat/' + item.to.userid)"
								class="uni-flex uni-row pd10 flex-middle width-between relative">
								<div class="uni-flex uni-row flex-middle">
									<div class="portrait">
										<el-avatar :src="item.to.portrait" :size="40" />
									</div>
									<div class="uni-flex uni-column pt5 pb5 ml10 f-13 width-surplus">
										<p class="uni-flex uni-row flex-middle width-between">
											<span class="bold line-clamp-1 name">
												{{ item.to.nickname || item.to.username }}</span>
											<label class="f-10 f-gray">{{ item.created }}</label>
										</p>
										<span v-if="item.content.indexOf('<img') > -1" class="f-gray f-10 mt5">[图片]</span>
										<span v-else class="f-gray line-clamp-1 f-10 mt5">
											{{ item.content.replace(/<[^>]+>/g, '') }}</span>
									</div>
								</div>
								<div v-if="item.unreads > 0" class="absolute f-white f-10">
									<span v-if="item.unreads > 10" class="unread">10+</span>
									<span v-else class="unread">{{ item.unreads }}</span>
								</div>
							</div>
							<div v-else @click="redirect('/webim/chat/' + item.to.userid + '/' + parseInt(item.store_id))"
								class="uni-flex uni-row pd10 flex-middle width-between relative">
								<div class="uni-flex uni-row flex-middle">
									<div class="portrait">
										<el-avatar :src="item.to.portrait" :size="40" />
									</div>
									<div class="uni-flex uni-column pt5 pb5 ml10 f-13 width-surplus">
										<p class="uni-flex uni-row flex-middle width-between">
											<span class="bold line-clamp-1 name">
												{{ item.store_name || (item.to.nickname || item.to.username) }}
											</span>
											<label class="f-10 f-gray">{{ item.created }}</label>
										</p>
										<span v-if="item.content.indexOf('<img') > -1" class="f-gray f-10 mt5">[图片]</span>
										<span v-else class="f-gray line-clamp-1 f-10 mt5">
											{{ item.content.replace(/<[^>]+>/g, '') }}</span>
									</div>
								</div>
								<div v-if="item.unreads > 0" class="absolute f-white f-10">
									<span v-if="item.unreads > 10" class="unread">10+</span>
									<span v-else class="unread">{{ item.unreads }}</span>
								</div>
							</div>
						</div>
					</el-scrollbar>
				</el-col>
				<el-col :span="13" class="gallery">
					<div class="uni-flex uni-row hd ml10 flex-middle">
						<i v-if="store.store_id" class="iconfont icon-shangjialiebiaoicon mr5"></i>
						<p class="bold">{{ store.store_id ? gallery.touser : '与“' + gallery.touser + '”会话中' }}</p>
					</div>
					<div class="bd ml10">
						<el-scrollbar ref="scrollbarRef" height="400px">

							<div v-for="(item) in gallery.logs" class="uni-flex uni-column pd10 mt10">
								<div v-if="item.fromid == visitor.userid" class="uni-flex uni-row mb10 flex-end">
									<div class="mr10 text-right">
										<div>
											<span class="f-gray f-12">{{ item.created }}</span>
										</div>
										<div class="detail-info pd10 mb10 bgp round-edge inline-block"
											v-html="item.content">
										</div>
									</div>
									<div class="portrait">
										<el-avatar :src="item.from.portrait" :size="40" />
									</div>
								</div>

								<div v-else class="uni-flex uni-row mb10">
									<div class="portrait">
										<el-avatar :src="item.from.portrait" :size="40" />
									</div>
									<div class="ml10">
										<div class="flex-middle">
											<span>{{ item.from.nickname || item.from.username }}</span>
											<span class="ml10 f-gray f-12">{{ item.created }}</span>
										</div>
										<div class="detail-info pd10 mb10 bgf round-edge inline-block"
											v-html="item.content">
										</div>
									</div>
								</div>
							</div>

						</el-scrollbar>
					</div>
					<div class="fd mb20">
						<div class="pl10 pt10 relative ml10">
							<!-- <Editor v-model="form.content" :defaultConfig="editorConfig" mode="simple" class="textarea" /> -->
							<div class="meta absolute uni-flex uni-row flex-middle">
								<el-upload action="#" :show-file-list="false" :auto-upload="false" :on-change="fileUpload"
									class="mr10">
									<i class="iconfont icon-tupian pointer f-18 f-gray"></i>
								</el-upload>
								<el-popover placement="top-start" :width="200" trigger="click">
									<template #reference>
										<i class="iconfont icon-biaoqing pointer f-18 f-gray"></i>
									</template>
									<gridEmoji @click="addEmoji" />
								</el-popover>
							</div>
							<el-input v-model="form.content" type="textarea" class="textarea" placeholder="请输入消息，按发送键发送"
								resize="none" @keyup.enter.native="send" />
						</div>
						<div class="text-right ml10 mr10">
							<el-button @click="send" class="send" type="primary">发送(Enter)</el-button>
						</div>
					</div>
				</el-col>
				<el-col :span="6">
					<div class="pd10 mg5">
						<div style="height:26px;"></div>
						<div class="bgf round-edge mg5" v-loading="loading">
							<div v-if="store.store_id" class="uni-flex uni-row pd10">
								<p><img :src="store.store_logo" width="44" /></p>
								<p class="ml10 f-13">
									<span class="flex-middle">
										<label class="mr5">{{ store.store_name }}</label>
										<img :src="store.credit_image" width="13" />
									</span>
									<router-link :to="'/store/index/' + store.store_id" class="rlink block mt5 f-12">
										<el-tag>进入店铺</el-tag>
									</router-link>
								</p>
							</div>
							<el-scrollbar v-if="orders.length > 0" :height="store.store_id ? '456px' : '524px'">
								<div class="orders pd10">
									<div class="hd bold pb10">{{ store.store_id ? '我的订单' : '已下单' }}</div>
									<div class="bd">
										<div v-for="(order, index) in orders"
											:class="['item round-edge bgp pl10 pt5 pr10 pb5', index == orders.length - 1 ? '' : 'mb10']">
											<p class="f-12 f-gray mb5">订单号：{{ order.order_sn }}</p>
											<div v-for="(item) in order.items" class="uni-flex uni-row">
												<p><img class="round-edge" :src="item.goods_image" width="66" /></p>
												<div class="f-12 ml5">
													<p class="line-clamp-1" :title="item.goods_name">{{ item.goods_name }}
													</p>
													<p class="f-red mt5 mb5">{{ currency(item.price) }}</p>
													<el-tag type="warning">{{ translator(order.status) }}</el-tag>
												</div>
											</div>
										</div>
									</div>
								</div>
							</el-scrollbar>
							<div v-else class="flex-middle flex-center f-gray"
								:style="store.store_id ? 'height:456px' : 'height:524px'">暂无订单/商品信息</div>
						</div>
					</div>
				</el-col>
			</el-row>
		</div>
		<myfoot></myfoot>
	</div>
</template>

<script setup>
import { ref, reactive, onMounted, getCurrentInstance } from 'vue'
import { useRoute } from 'vue-router'
import { ElMessage, ElScrollbar } from 'element-plus'
//import { Editor } from '@wangeditor/editor-for-vue'
//import '@wangeditor/editor/dist/css/style.css'

import { redirect, currency, translator } from '@/common/util.js'
import { webimList, webimLogs, webimSend } from '@/api/webim.js'
import { uploadFile } from '@/api/upload.js'
import { storeRead } from '@/api/store.js'
import { userRead } from '@/api/user.js'
import { myOrderList, sellerOrderList } from '@/api/order.js'

import myhead from '@/pages/layout/header/home.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import gridEmoji from '@/components/datagrid/emoji.vue'

const scrollbarRef = ref(ElScrollbar)
const { proxy } = getCurrentInstance()

// 需要输入富文本使用editor
//const editorConfig = { placeholder: '请输入消息，按发送键发送' }
const loading = ref(false)
const route = useRoute()
const visitor = ref({})
const gallery = reactive({ touser: '', list: [], logs: [] })
const form = reactive({ content: '' })
const store = ref({})
const orders = ref({})

onMounted(() => {
	visitor.value = JSON.parse(localStorage.getItem('visitor')) || {}

	//getList()
	setInterval(() => {
		getList()
	}, 4000)

	getlogs()
	setInterval(() => {
		getlogs()
	}, 3000)

	if (route.params.store_id > 0) {
		storeRead(route.params, (data) => {
			store.value = data
			gallery.touser = data.store_name

			myOrderList({ store_id: data.store_id, queryitem: true }, (res) => {
				orders.value = res.list
			}, loading)
		})
	} else {
		userRead({ userid: route.params.id }, (data) => {
			gallery.touser = data.nickname || data.username

			sellerOrderList({ buyer_name: data.username, queryitem: true }, (res) => {
				orders.value = res.list
			}, loading)
		})
	}
})

const send = () => {
	if (!route.params.id) {
		ElMessage.error('请选择会话用户')
	}
	else if (form.content.replace(/<[^>]+>/g, '').replace(/\s*/g, "") == '') {
		ElMessage.error('内容不能为空')
	} else {
		webimSend(Object.assign(form, { toid: route.params.id, store_id: parseInt(route.params.store_id) }), (data) => {
			form.content = ''
		})
	}
}
const fileUpload = (file) => {
	uploadFile(file.raw, { folder: 'webim/' }, (data) => {
		let html = '<img width="100" src="' + data.fileUrl + '" />'
		webimSend({ content: html, toid: route.params.id, store_id: parseInt(route.params.store_id) }, (res) => {
			//getlogs()
		})
	})
}

const addEmoji = (target) => {
	form.content += target.innerHTML
}

function getList() {
	webimList({ limit: 50 }, (data) => {
		gallery.list = data
	})
}

function getlogs() {
	webimLogs({ toid: route.params.id, page_size: 100 }, (data) => {

		if (data.list.length > 0) {
			if (gallery.logs.length == 0 || (gallery.logs.length > 0 && (data.list[data.list.length - 1].id > gallery.logs[gallery.logs.length - 1].id))) {
				gallery.logs = data.list

				proxy.$nextTick(() => {
					setTimeout(() => {
						if (scrollbarRef.value) scrollbarRef.value.setScrollTop(10000000)
					}, 1000);
				})
			}
		}
	})
}

</script>
<style scoped>
.main {
	position: absolute;
	height: 100%;
	width: 100%;
	top: 0;
	left: 0;
	overflow-y: auto;
	background-color: #e3e7e2;
	z-index: 1;
}

.header {
	width: 100%;
	height: 35px;
	line-height: 35px;
	background-color: #f2f2f2;
	border-bottom: 1px #eeeeee solid;
}

.chatbox .wraper {
	margin: 50px auto 0;
	background-color: #f0f2f5;
}

.chatbox .panel {
	background-color: #f7f8fa;
	border-radius: 5px 0 0 5px;
}

.chatbox .panel .item:hover {
	background-color: #fefafa;
}

.chatbox .panel .name {
	width: 90px;
}

.chatbox .panel .absolute {
	right: 8px;
	top: 2px;
}

.gallery .hd,
.gallery .bd {
	border-bottom: 1px #ddd solid;
}

.gallery .hd,
.panel .hd {
	height: 44px;
	line-height: 44px;
}

.gallery .detail-info {
	word-break: break-all;
	text-align: left;
}

.gallery .detail-info.bgp {
	background-color: rgb(185, 219, 220);
}

.gallery .fd .send {
	border-radius: 5px;
}

.gallery .fd .textarea {
	margin-top: 22px;
	padding-right: 10px;
}

:deep() .gallery .fd .textarea textarea {
	background: none;
	box-shadow: none;
	margin: 5px 0;
	height: 50px;
	padding: 5px 5px 5px 0;
}

.unread {
	background-color: var(--my-color-red);
	border-radius: 10px;
	line-height: 16px;
	padding: 0 4px;
	font-weight: bold;
}

:deep() .w-e-text-placeholder {
	top: 5px;
	left: 0;
	font-style: normal;
}

:deep() .w-e-text-container {
	padding: 5px 0;
}

:deep() .w-e-text-container .w-e-scroll {
	height: 42px;
}

:deep() .w-e-text-container p {
	margin: 0;
}
</style>
