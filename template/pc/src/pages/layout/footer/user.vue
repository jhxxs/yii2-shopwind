<template>
	<blocks :page="page" :footer="true" :exclude="exclude"></blocks>
	<wechat />
	<chat />
	<el-backtop :right="10" :bottom="100" :visibility-height="50" />
</template>

<script setup>
import blocks from '@/components/datagrid/blocks.vue'
import chat from '@/components/datagrid/chat.vue'
import wechat from '@/components/datagrid/wechat.vue'

const props = defineProps({
	page: {
		type: String,
		default: ''
	},
	exclude: {
		type: Array,
		default() {
			return []
		}
	}
})
</script>
