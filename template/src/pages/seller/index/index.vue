<template>
	<myhead></myhead>
	<div class="main w pt10">
		<el-row :gutter="12">
			<el-col :span="4">
				<menus></menus>
			</el-col>
			<el-col :span="14">

				<!--店铺信息-->
				<div class="round-edge bgf pd10">
					<div class="uni-flex uni-row width-between pd10">
						<div class="width-surplus pt5 vertical-middle">
							<h3 class="inline-block f-16 mr10">{{ store.store_name }}</h3>
							<!-- <span class="f-10 f-number">LV3</span> 
							<i class="iconfont icon-shangsheng-1 f-10 f-green mr10"></i>-->
							<el-button v-if="store.state == 1" class="opening f-10" size="small">正常营业</el-button>
						</div>
						<div v-if="store.category" class="uni-flex uni-row vertical-middle">
							<i class="iconfont icon-fenleimulu mr5" style="margin-top:2px"></i>
							<span class="mr10 f-13">经营类目</span>
							<el-breadcrumb separator="/">
								<el-breadcrumb-item v-for="item in store.category" class="f-gray f-13">
									{{ item }}
								</el-breadcrumb-item>
							</el-breadcrumb>
						</div>
					</div>

					<el-row class="mt10 pb10 pt20" v-if="dynamiceval.comprehensive.percentage">
						<el-col :span="6" class="center f-12">
							<el-progress type="dashboard" :width="80" :stroke-width="6"
								:percentage="Number((dynamiceval.comprehensive.percentage).replace('%', ''))">{{(dynamiceval.comprehensive.percentage).replace('%',
								'')}}分</el-progress>
						</el-col>
						<el-col :span="2">
							<el-divider style="height:90%" direction="vertical"></el-divider>
						</el-col>
						<el-col :span="16">
							<el-row>

								<el-col :span="8">
									<p class="f-13">
										<span>{{ dynamiceval.goods.label }}</span>
										<!-- <i class="iconfont icon-wenhao-xianxingyuankuang f-12 pl5"></i> -->
									</p>
									<p class="bold f-20 pb10 pt10 mt5 f-number">{{ dynamiceval.goods.value }}</p>
									<p class="f-12 flex-middle">
										<span class="f-gray mr5">与同行比</span>
										<span v-if="dynamiceval.goods.compare.value > 0"
											class="f-green f-number">{{ dynamiceval.goods.compare.value }}</span>
										<el-icon v-if="dynamiceval.goods.compare.name == 'low'" :size="12"
											color="#00CC00">
											<bottom />
										</el-icon>
										<el-icon v-else-if="dynamiceval.goods.compare.name == 'high'" :size="12"
											color="#fc2b34">
											<top />
										</el-icon>
										<label v-else class="f-c55">持平</label>
									</p>
								</el-col>

								<el-col :span="8">
									<p class="f-13">
										<span>{{ dynamiceval.shipped.label }}</span>
										<!-- <i class="iconfont icon-wenhao-xianxingyuankuang f-12 pl5"></i> -->
									</p>
									<p class="bold f-20 pb10 pt10 mt5 f-number">{{ dynamiceval.shipped.value }}</p>
									<p class="f-12 flex-middle">
										<span class="f-gray mr5">与同行比</span>
										<span v-if="dynamiceval.shipped.compare.value > 0"
											class="f-green f-number">{{ dynamiceval.shipped.compare.value }}</span>
										<el-icon v-if="dynamiceval.shipped.compare.name == 'low'" :size="12"
											color="#00CC00">
											<bottom />
										</el-icon>
										<el-icon v-else-if="dynamiceval.shipped.compare.name == 'high'" :size="12"
											color="#fc2b34">
											<top />
										</el-icon>
										<label v-else class="f-c55">持平</label>
									</p>
								</el-col>
								<el-col :span="8">
									<p class="f-13">
										<span>{{ dynamiceval.service.label }}</span>
										<!-- <i class="iconfont icon-wenhao-xianxingyuankuang f-12 pl5"></i> -->
									</p>
									<p class="bold f-20 pb10 pt10 mt5 f-number">{{ dynamiceval.service.value }}</p>
									<p class="f-12 flex-middle">
										<span class="f-gray mr5">与同行比</span>
										<span v-if="dynamiceval.service.compare.value > 0"
											class="f-green f-number">{{ dynamiceval.service.compare.value }}</span>
										<el-icon v-if="dynamiceval.service.compare.name == 'low'" :size="12"
											color="#00CC00">
											<bottom />
										</el-icon>
										<el-icon v-else-if="dynamiceval.service.compare.name == 'high'" :size="12"
											color="#fc2b34">
											<top />
										</el-icon>
										<label v-else class="f-c55">持平</label>
									</p>
								</el-col>
							</el-row>
						</el-col>
					</el-row>
				</div>

				<!-- 代办事项 -->
				<div class="round-edge bgf pd10 mt20">
					<div class="bold f-16 pd10 mb20">待办事项</div>
					<el-row style="margin-left: 40px;">
						<el-col :span="6">
							<dl class="mb20 pt10">
								<dt @click="redirect('/seller/order/list?type=pending')"
									class="flex-middle mb10 pointer"><span>待付款</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</dt>
								<dd class="f-20 f-number bold">{{ reminds.pending || 0 }}</dd>
							</dl>
							<dl class="mb20 pt10">
								<dt @click="redirect('/seller/order/list?type=delivered')"
									class="flex-middle mb10 pointer"><span>待取货</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</dt>
								<dd class="f-20 f-number bold">{{ reminds.delivered || 0 }}</dd>
							</dl>
						</el-col>
						<el-col :span="6">
							<dl class="mb20 pt10">
								<dt @click="redirect('/seller/order/list?type=teaming')"
									class="flex-middle mb10 pointer"><span>待成团</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</dt>
								<dd class="f-20 f-number bold f-number">{{ reminds.teaming || 0 }}</dd>
							</dl>
							<dl class="mb20 pt10">
								<dt @click="redirect('/seller/order/list?type=finished')"
									class="flex-middle mb10 pointer"><span>交易完成</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</dt>
								<dd class="f-20 f-number bold">{{ reminds.finished || 0 }}</dd>
							</dl>
						</el-col>
						<el-col :span="6">
							<dl class="mb20 pt10">
								<dt @click="redirect('/seller/order/list?type=accepted')"
									class="flex-middle mb10 pointer"><span>待发货</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</dt>
								<dd class="f-20 f-number bold">{{ reminds.accepted || 0 }}</dd>
							</dl>
							<dl class="mb20 pt10">
								<dt @click="redirect('/seller/order/list?type=canceled')"
									class="flex-middle mb10 pointer"><span>交易关闭</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</dt>
								<dd class="f-20 f-number bold">{{ reminds.canceled || 0 }}</dd>
							</dl>
						</el-col>
						<el-col :span="6">
							<dl class="mb20 pt10">
								<dt @click="redirect('/seller/order/list?type=picking')"
									class="flex-middle mb10 pointer"><span>待配送</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</dt>
								<dd class="f-20 f-number bold">{{ reminds.picking || 0 }}</dd>
							</dl>
							<dl class="mb20 pt10">
								<dt @click="redirect('/seller/refund/list?status=GOING')"
									class="flex-middle mb10 pointer"><span>退款处理</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</dt>
								<dd class="f-20 f-number bold">{{ reminds.refund || 0 }}</dd>
							</dl>
						</el-col>
					</el-row>
				</div>

				<!-- 资金数据 -->
				<div class="round-edge bgf pd10 mt20">
					<div class="uni-flex uni-row pd10 mb20 width-between">
						<p class="f-16">
							<span class="bold">资金数据</span>
							<el-icon>
								<view />
							</el-icon>
						</p>
						<p class="f-gray f-12">更新时间：{{ getMoment().format('MM/DD HH:mm:ss') }}</p>
					</div>
					<el-row style="margin-left: 40px;">
						<el-col :span="8">
							<dl class="mb20">
								<dt @click="redirect('/deposit/index')" class="flex-middle mb10 pointer">
									<span>可用余额</span>
									<el-icon :size="14">
										<arrow-right />
									</el-icon>
								</dt>
								<dd class="f-20 f-number bold">{{ currency(deposit.money) }}</dd>
								<dd @click="redirect('/deposit/trade/drawal')" class="f-blue mt5 f-12 ml5 pointer">提现
								</dd>
							</dl>
						</el-col>
						<el-col :span="8">
							<dl class="mb20">
								<dt class="flex-middle mb10">
									<span class="pr5">不可提现</span>
									<el-icon :size="15" color="#dddddd">
										<question-filled />
									</el-icon>
								</dt>
								<dd class="f-20 f-number bold">{{ currency(deposit.nodrawal) }}</dd>
							</dl>

						</el-col>
						<el-col :span="8">
							<dl class="mb20">
								<dt class="flex-middle mb10">
									<span>冻结金额</span>
									<!-- <el-icon :size="14">
										<arrow-right />
									</el-icon> -->
								</dt>
								<dd class="f-20 f-number bold">{{ currency(deposit.frozen) }}</dd>
								<!-- <dd class="f-blue mt5 f-12 ml5">查看</dd> -->
							</dl>
						</el-col>
					</el-row>
				</div>

			</el-col>
			<el-col :span="6">
				<blocks page="sellerindex" :header="false" :footer="false"></blocks>
			</el-col>
		</el-row>
	</div>
	<myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { storeRead, storeDynamiceval } from '@/api/store.js'
import { articleList } from '@/api/article.js'
import { sellerOrderRemind } from '@/api/order.js'
import { depositRead } from '@/api/deposit.js'
import { redirect, currency } from '@/common/util.js'
import { getMoment } from '@/common/moment.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'
import blocks from '@/components/datagrid/blocks.vue'

const articles = ref([])
const store = ref({})
const reminds = ref({})
const deposit = ref({})
const dynamiceval = ref({ goods: {}, shipped: {}, service: {}, comprehensive: {} })

onMounted(() => {
	let visitor = JSON.parse(localStorage.getItem('visitor')) || {}

	storeRead({ store_id: visitor.userid }, (data) => {
		store.value = data
	})
	storeDynamiceval({ store_id: visitor.userid }, (data) => {
		if (data) { dynamiceval.value = data }
	})
	depositRead(null, (data) => {
		deposit.value = data
	})
	articleList({ page_size: 10 }, (data) => {
		articles.value = data.list
	})
	sellerOrderRemind(null, (data) => {
		reminds.value = data
	})
})
</script>

<style scoped>
.rboxdivider {
	width: 4px;
	height: 12px;
	background-color: #0066B3;
	border-radius: 4px 4px 4px 4px;
}

.el-button.opening {
	color: var(--el-button-hover-text-color);
	border-color: var(--el-button-hover-border-color);
	background-color: var(--el-button-hover-bg-color);
}

:deep() .el-breadcrumb__separator {
	font-weight: normal;
	margin: 0 5px;
}
</style>
