<template>
	<div class="mymenus pd10 round-edge bgf mb20">
		<router-link to="/my/index" class="rlink">
			<i class="iconfont icon-shouye"></i><span class="bold ml5">首页</span>
		</router-link>
		<dl class="bt pt10 mt10">
			<dt class="pt10 flex-middle">
				<i class="iconfont icon-zhanghu"></i><span class="bold ml5">账户</span>
			</dt>
			<dd class="uni-flex uni-row flex-wrap pt10 mt5 ml10 pl5 f-c55">
				<router-link to="/my/setting/profile"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/setting/profile' ? 'f-red' : '']">个人资料
				</router-link>
				<router-link to="/my/setting/password"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/setting/password' ? 'f-red' : '']">修改密码
				</router-link>
				<router-link to="/my/setting/phone"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/setting/phone' ? 'f-red' : '']">手机设置
				</router-link>
				<router-link to="/my/address/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/address/list' ? 'f-red' : '']">收货地址
				</router-link>
				<router-link to="/my/mailbox/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/mailbox/list' ? 'f-red' : '']">短消息
				</router-link>
			</dd>
		</dl>
		<dl class="bt pt10 mt10">
			<dt class="pt10 flex-middle">
				<i class="iconfont icon-yue"></i><span class="bold ml5">资产</span>
			</dt>
			<dd class="uni-flex uni-row flex-wrap pt10 mt5 ml10 pl5 f-c55">
				<router-link to="/deposit/index"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/deposit/index' ? 'f-red' : '']">钱包账户
				</router-link>
				<router-link to="/deposit/trade/drawal"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/deposit/trade/drawal' ? 'f-red' : '']">余额提现
				</router-link>
				<router-link to="/deposit/setting/pay"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/deposit/setting/pay' ? 'f-red' : '']">支付设置
				</router-link>
				<router-link to="/my/bank/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/bank/list' ? 'f-red' : '']">提现设置
				</router-link>
				<router-link to="/deposit/trade/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/deposit/trade/list' ? 'f-red' : '']">交易记录
				</router-link>
				<router-link to="/deposit/trade/record"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/deposit/trade/record' ? 'f-red' : '']">财务明细
				</router-link>
				<router-link to="/my/integral/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/integral/list' ? 'f-red' : '']">我的积分
				</router-link>
				<router-link to="/my/coupon/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/coupon/list' ? 'f-red' : '']">优惠券
				</router-link>
				<router-link to="/my/cashcard/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/cashcard/list' ? 'f-red' : '']">充值卡
				</router-link>
			</dd>
		</dl>
		<dl class="bt pt10 mt10">
			<dt class="pt10 flex-middle">
				<i class="iconfont icon-dingdan"></i><span class="bold ml5">订单</span>
			</dt>
			<dd class="uni-flex uni-row flex-wrap pt10 mt5 ml10 pl5 f-c55">
				<router-link to="/my/order/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/order/list' ? 'f-red' : '']">我的订单
				</router-link>
				<router-link to="/my/evaluate/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/evaluate/list' ? 'f-red' : '']">我的评价
				</router-link>
			</dd>
		</dl>
		<dl class="pt10">
			<dt class="pt10 flex-middle">
				<i class="iconfont icon-shouhou"></i><span class="bold ml5">售后</span>
			</dt>
			<dd class="uni-flex uni-row flex-wrap pt10 mt5 ml10 pl5 f-c55">
				<router-link to="/my/refund/list"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/refund/list' ? 'f-red' : '']">我的退款
				</router-link>
			</dd>
		</dl>
		<dl class="pt10">
			<dt class="pt10 flex-middle">
				<i class="iconfont icon-shoucang"></i><span class="bold ml5">收藏</span>
			</dt>
			<dd class="uni-flex uni-row flex-wrap pt10 mt5 ml10 pl5 f-c55">
				<router-link to="/my/favorite/goods"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/favorite/goods' ? 'f-red' : '']">商品收藏
				</router-link>
				<router-link to="/my/favorite/store"
					:class="['mt5 ml5 mr10 mb10 rlink', curmenu == '/my/favorite/store' ? 'f-red' : '']">店铺收藏
				</router-link>
			</dd>
		</dl>
	</div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const curmenu = ref('')
const router = useRouter();
curmenu.value = router.currentRoute.value.path
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
