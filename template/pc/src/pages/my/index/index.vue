<template>
	<myhead></myhead>
	<div class="main w pt10">
		<el-row :gutter="12">
			<el-col :span="4">
				<menus></menus>
			</el-col>
			<el-col :span="20">
				<div class="round-edge bgf pd10">
					<div class="uni-flex uni-row pd10 flex-middle">
						<div class="portrait pt10 pb10">
							<img :src="visitor.portrait" width="80" height="80" />
						</div>
						<div class="ml20 pt10 pb10">
							<p>
								<strong class="f-18">{{ visitor.nickname || visitor.username }}</strong>
								<i @click="dialogVisible = true" class="iconfont icon-bianji f-13 ml10 pointer f-blue"></i>
							</p>
							<p class="f-13 f-gray mt10">上次登录时间：{{ visitor.last_login||'无' }} <span class="ml10 mr10"></span>上次登录IP：{{ visitor.last_ip||'无' }}
							</p>
						</div>
					</div>
				</div>
				<div class="round-edge bgf pd10 mt20">
					<div class="bold f-16 pd10 mb20">我的订单</div>
					<el-row style="margin-left: 40px;">
						<el-col :span="6">
							<dl class="mb20 pt10">
								<router-link to="/my/order/list?type=pending" class="rlink flex-middle mb10">
									<span>待付款</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</router-link>
								<dd class="f-20 f-number bold">{{ orders.pending }}
								</dd>
							</dl>
							<dl class="mb20 pt10">
								<router-link to="/my/order/list?type=picking" class="rlink flex-middle mb10">
									<span>待配送</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</router-link>
								<dd class="f-20 f-number bold">{{ orders.picking }}</dd>
							</dl>
						</el-col>
						<el-col :span="6">
							<dl class="mb20 pt10">
								<router-link to="/my/order/list?type=accepted" class="rlink flex-middle mb10">
									<span>待发货</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</router-link>
								<dd class="f-20 f-number bold">{{ orders.accepted }}</dd>
							</dl>

							<dl class="mb20 pt10">
								<router-link to="/my/order/list?type=delivered" class="rlink flex-middle mb10">
									<span>待取货</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</router-link>
								<dd class="f-20 f-number bold">{{ orders.delivered }}</dd>
							</dl>
						</el-col>
						<el-col :span="6">
							<dl class="mb20 pt10">
								<router-link to="/my/order/list?type=teaming" class="rlink flex-middle mb10">
									<span>待成团</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</router-link>
								<dd class="f-20 f-number bold">{{ orders.teaming }}</dd>
							</dl>
							<dl class="mb20 pt10">
								<router-link to="/my/order/list?type=shipped" class="rlink flex-middle mb10">
									<span>待签收</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</router-link>
								<dd class="f-20 f-number bold">{{ orders.shipped }}</dd>
							</dl>
						</el-col>
						<el-col :span="6">
							<dl class="mb20 pt10">
								<router-link to="/my/order/list?type=finished" class="rlink flex-middle mb10">
									<span>待评价</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</router-link>
								<dd class="f-20 f-number bold f-number">{{ orders.evaluation }}</dd>
							</dl>
							<div class="mb20 pt10">
								<router-link to="/my/refund/list?status=GOING" class="rlink flex-middle mb10">
									<span>退款中</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</router-link>
								<dd class="f-20 f-number bold">{{ orders.refund }}</dd>
							</div>
						</el-col>
					</el-row>
				</div>

				<div class="round-edge bgf pd10 mt20">
					<div class="uni-flex uni-row pd10 mb20 width-between">
						<p class="f-16">
							<span v-if="!visitor.store_id" class="bold">免费入驻</span>
							<span v-else class="bold">店铺管理</span>
							<el-icon>
								<view />
							</el-icon>
						</p>
					</div>
					<el-row>
						<div class="pl10 pb20 f-gray f-13">
							<p v-if="!visitor.store_id">
								您还没有开通店铺，支持个人/企业入驻，可点此 <router-link to="/store/apply"
									class="rlink f-blue">一键入驻</router-link> 轻松做生意！</p>
							<p v-else>点此进入 <router-link to="/seller/index" class="rlink f-blue">商家中心</router-link>
								管理您的商品和订单</p>
						</div>
					</el-row>
				</div>
			</el-col>
		</el-row>
	</div>

	<build title="修改昵称" :visible="dialogVisible" :data="visitor" @close="dialogClose"></build>

	<myfoot></myfoot>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { myOrderRemind } from '@/api/order.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'
import build from '@/components/dialog/user/build.vue'

const visitor = ref({})
const orders = ref({})
const dialogVisible = ref(false)

onMounted(() => {
	visitor.value = JSON.parse(localStorage.getItem('visitor'))

	myOrderRemind(null, (data) => {
		orders.value = data
	})
})

const dialogClose = (value) => {
	dialogVisible.value = false
	if (value) {
		localStorage.setItem('visitor', JSON.stringify(value))
	}
}

</script>

<style scoped>
.portrait img {
	border-radius: 100%;
}
</style>
