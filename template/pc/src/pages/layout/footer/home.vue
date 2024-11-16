<template>
	<blocks :page="page" :footer="true" :exclude="exclude"></blocks>
	<el-backtop :right="10" :bottom="100" :visibility-height="500" />
	<chat></chat>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import blocks from '@/components/datagrid/blocks.vue'
import chat from '@/components/datagrid/chat.vue'

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
