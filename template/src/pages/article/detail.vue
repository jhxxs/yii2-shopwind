<template>
    <myhead :exclude="['category', 'imagead']"></myhead>
    <div class="main w">
        <div v-if="article" class="round-edge bgf pd10 mt20 mb20">
            <div class="pd10 mb10 center title">
                <h3 class="pd10 f-18">{{ article.title }}</h3>
                <p class="pd10 f-gray">{{ article.add_time }}</p>
            </div>
            <div class="content pd10">
                <div class="detail-info pb20 mb20 pd10" v-html="article.description"></div>
            </div>
        </div>
        <div v-else-if="!loading" class="round-edge bgf mt20 ml10 mb20 f-gray center">
            <el-empty :image-size="160" description="文章不存在" />
        </div>
    </div>
    <myfoot></myfoot>
</template>
<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { articleRead } from '@/api/article.js'

import myhead from '@/pages/layout/header/home.vue'
import myfoot from '@/pages/layout/footer/home.vue'

const loading = ref(true)
const route = useRoute()
const article = ref()

onMounted(() => {
    articleRead({ article_id: route.params.id }, (data) => {
        article.value = data
    }, loading)
})
</script>
<style scoped>
.title {
    border-bottom: 1px #ddd dotted;
}

.detail-info {
    line-height: 24px;
}
</style>