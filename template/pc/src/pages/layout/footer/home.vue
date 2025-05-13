<template>
	<blocks :page="page" :footer="true" :exclude="exclude"></blocks>
	<wechat />
	<chat />
	<el-backtop :right="10" :bottom="100" :visibility-height="500" />
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import blocks from '@/components/datagrid/blocks.vue';
import chat from '@/components/datagrid/chat.vue';
import wechat from '@/components/datagrid/wechat.vue';

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
