<template>
	<blocks :page="route.params.id"></blocks>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useRoute } from 'vue-router'
import blocks from '@/components/datagrid/blocks.vue'

const route = useRoute()
</script>
