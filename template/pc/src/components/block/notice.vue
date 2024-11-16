<template>
    <div class="noticebox" :style="{ '--bgcolor': options.bgcolor || '', '--space': options.space + 'px' }"
        v-loading="loading">
        <div :class="['wraper round-edge bgf', options.showfull == 1 ? 'ml10 mr10' : 'w']">
            <div class="title uni-flex uni-row width-between pd10 flex-middle">
                <span class="rboxdivider inline-block mr5"></span>
                <h3 class="f-13 bold width-surplus">{{ options.title || '资讯' }}</h3>
            </div>
            <div class="content pl10 pr10 pb10">
                <p v-for="item in gallery" class="pt5 pb5">
                    <router-link :to="'/article/detail/' + item.article_id" class="f-13 f-c55 rlink">
                        {{ item.title }}
                    </router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { articleList } from '@/api/article.js'

const props = defineProps({
    options: {
        type: Object,
        default() {
            return {}
        }
    }
})
const loading = ref(false)
const gallery = ref([])

onMounted(() => {
    articleList({ items: props.options.source == 'choice' ? props.options.items : '', cate_id: props.options.source == 'category' ? props.options.cate_id : 0, page_size: props.options.quantity }, (data) => {
        gallery.value = data.list
    }, loading)
})

</script>
<style scoped>
.noticebox {
    background: var(--bgcolor);
    padding: var(--space) 0;
}

.noticebox .rboxdivider {
    width: 4px;
    height: 12px;
    background-color: #0066B3;
    border-radius: 4px 4px 4px 4px;
}
</style>

