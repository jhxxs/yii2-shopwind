<template>
	<div class="mymenus pd10 round-edge bgf mb20">
		<router-link to="/seller/index" class="rlink">
			<i class="iconfont icon-shouye"></i><span class="bold ml5">首页</span>
		</router-link>
		<dl class="bt pt10 mt10">
			<dt class="pt10 flex-middle">
				<i class="iconfont icon-shangpin"></i><span class="bold ml5">商品</span>
			</dt>
			<dd class="uni-flex uni-row flex-wrap pt10 mt5 ml10 pl5 f-c55">
				<router-link to="/seller/goods/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/goods/list' ? 'f-red' : '']">商品列表
				</router-link>
				<router-link to="/seller/goods/build"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/goods/build' ? 'f-red' : '']">发布商品
				</router-link>
				<router-link to="/seller/goods/list?closed=1"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/goods/list?closed=1' ? 'f-red' : '']">禁售商品
				</router-link>
				<router-link to="/seller/goods/stockwarn"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/goods/stockwarn' ? 'f-red' : '']">库存预警
				</router-link>
				<router-link to="/seller/goods/picker"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/goods/picker' ? 'f-red' : '']">采集商品
				</router-link>
			</dd>
		</dl>
		<dl class="pt10">
			<dt class="pt10 flex-middle">
				<i class="iconfont icon-dingdan"></i><span class="bold ml5">订单</span>
			</dt>
			<dd class="uni-flex uni-row flex-wrap pt10 mt5 ml10 pl5 f-c55">
				<router-link to="/seller/order/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/order/list' ? 'f-red' : '']">订单管理
				</router-link>
				<router-link to="/seller/evaluate/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/evaluate/list' ? 'f-red' : '']">评价管理
				</router-link>
				<router-link to="/seller/sms/index"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/sms/index' ? 'f-red' : '']">短信提醒
				</router-link>
			</dd>
		</dl>
		<dl class="pt10">
			<dt class="pt10 flex-middle">
				<i class="iconfont icon-shouhou"></i><span class="bold ml5">售后</span>
			</dt>
			<dd class="uni-flex uni-row flex-wrap pt10 mt5 ml10 pl5 f-c55">
				<router-link to="/seller/refund/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/refund/list' ? 'f-red' : '']">退款管理
				</router-link>
				<router-link to="/webim/chat"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/webim/chat' ? 'f-red' : '']">客服管理
				</router-link>
			</dd>
		</dl>
		<dl class="pt10">
			<dt class="pt10 flex-middle">
				<i class="iconfont icon-wuliu"></i><span class="bold ml5">物流</span>
			</dt>
			<dd class="uni-flex uni-row flex-wrap pt10 mt5 ml10 pl5 f-c55">
				<router-link to="/seller/delivery/build"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/delivery/build' ? 'f-red' : '']">运费设置
				</router-link>
				<router-link to="/seller/delivery/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/delivery/list' ? 'f-red' : '']">运费模板
				</router-link>
				<router-link to="/seller/fullfree/index"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/fullfree/index' ? 'f-red' : '']">包邮设置
				</router-link>
				<router-link to="/seller/delivery/timer"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/delivery/timer' ? 'f-red' : '']">配送时效
				</router-link>
			</dd>
		</dl>
		<dl class="bt mt10 pt10">
			<dt class="pt10 flex-middle">
				<i class="iconfont icon-yingxiao1"></i><span class="bold ml5">营销</span>
			</dt>
			<dd class="uni-flex uni-row flex-wrap pt10 mt5 ml10 pl5 f-c55">
				<router-link to="/seller/teambuy/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/teambuy/list' ? 'f-red' : '']">多人拼团
				</router-link>
				<router-link to="/seller/mealbuy/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/mealbuy/list' ? 'f-red' : '']">搭配购
				</router-link>
				<router-link to="/seller/limitbuy/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/limitbuy/list' ? 'f-red' : '']">限时秒杀
				</router-link>
				<router-link to="/seller/fullprefer/index"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/fullprefer/index' ? 'f-red' : '']">满优惠
				</router-link>
				<router-link to="/seller/wholesale/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/wholesale/list' ? 'f-red' : '']">批发价
				</router-link>
				<router-link to="/seller/exclusive/index"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/exclusive/index' ? 'f-red' : '']">手机专享
				</router-link>
				<router-link to="/seller/fullfree/index"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/fullfree/index' ? 'f-red' : '']">满包邮
				</router-link>
				<router-link to="/seller/coupon/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/coupon/list' ? 'f-red' : '']">优惠券
				</router-link>
			</dd>
		</dl>
		<dl class="pt10">
			<dt class="pt10 flex-middle">
				<i class="iconfont icon-shangjialiebiaoicon"></i><span class="bold ml5">店铺</span>
			</dt>
			<dd class="uni-flex uni-row flex-wrap pt10 mt5 ml10 pl5 f-c55">
				<router-link to="/seller/store/setting"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/store/setting' ? 'f-red' : '']">基本设置
				</router-link>
				<router-link to="/seller/store/category"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/store/category' ? 'f-red' : '']">商品分类
				</router-link>
				<router-link to="/seller/store/swiper"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/store/swiper' ? 'f-red' : '']">店铺轮播
				</router-link>
				<router-link to="/seller/store/banner"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/store/banner' ? 'f-red' : '']">店招设置
				</router-link>
				<router-link to="/seller/store/map"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/store/map' ? 'f-red' : '']">店铺位置
				</router-link>
				<router-link to="/seller/store/codpay"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/seller/store/codpay' ? 'f-red' : '']">货到付款
				</router-link>
				<router-link :to="'/store/index/' + visitor.store_id" class="mt5 ml5 mr10 mb10 rlink">查看店铺</router-link>
			</dd>
		</dl>
	</div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const visitor = ref({})
const curmenu = ref('')
const router = useRouter()
curmenu.value = router.currentRoute.value.path
visitor.value = JSON.parse(localStorage.getItem('visitor'))
</script>

<style scoped>
.mymenus {
	padding: 16px;
	font-size: 13px;
}

.mymenus dd .rlink {
	min-width: 52px;
}
</style>
