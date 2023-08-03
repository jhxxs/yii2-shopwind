<template>
	<myhead page="index"></myhead>
	<blocks page="index" :header="false" :footer="false"></blocks>
	<myfoot page="index"></myfoot>
</template>

<script setup>
import blocks from '@/components/datagrid/blocks.vue'
import myhead from '@/pages/layout/header/home.vue'
import myfoot from '@/pages/layout/footer/home.vue'
</script>
