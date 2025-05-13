<template>
	<blocks :page="page" :footer="true" :exclude="exclude"></blocks>


	<el-popover :virtual-ref="buttonRef" trigger="hover" virtual-triggering placement="top" width="300px">
		<img src="@/assets/images/wechat_qrcopde.png" style="width: 300px;" />
	</el-popover>

	<el-backtop :right="10" :bottom="200" :visibility-height="0" class="wechat-backtop">

		<div class="wechat-icon-wrapper" @click.stop="openWechatQRcode" ref="buttonRef">
			<img src="@/assets/images/wechat_en.png" />
		</div>
	</el-backtop>

	<el-backtop :right="10" :bottom="100" :visibility-height="500" />
	<chat />
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import blocks from '@/components/datagrid/blocks.vue';
import chat from '@/components/datagrid/chat.vue';

const props = defineProps({
	page: {
		type: String,
		default: ''
	},
	exclude: {
		type: Array,
		default() {
			return [];
		}
	}
});

const buttonRef = ref()

function openWechatQRcode() {

}
</script>
<style lang="scss">
.wechat-backtop {
	padding: 0 !important;

	.wechat-icon-wrapper {
		cursor: pointer;
		width: 100%;
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	img {
		width: 20px;
		height: 20px;
	}
}
</style>
