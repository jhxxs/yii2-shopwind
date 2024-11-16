<template>
	<blocks :page="page" :header="true" :exclude="exclude"></blocks>
</template>

<script setup>
import blocks from '@/components/datagrid/blocks.vue'

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
