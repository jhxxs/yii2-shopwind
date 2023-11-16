<template>
  <el-config-provider :locale="zhCN">
    <router-view :key="$route.fullPath" />
  </el-config-provider>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
//import { useMeta } from 'vue-meta'

import zhCN from 'element-plus/lib/locale/lang/zh-cn'
import 'element-plus/dist/index.css'
</script>

<style>
/* 全局CSS */
@import '@/assets/css/main.css';

/* 引入阿里巴巴矢量图 */
@import url("//at.alicdn.com/t/c/font_3292940_1ei7l59c0ct.css");
</style>
