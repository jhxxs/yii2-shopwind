<template>
    <div class="swiperbox"
        :style="{ '--bgcolor': options.bgcolor, '--height': options.height || '512px', '--space': options.space + 'px' }"
        v-loading="loading">
        <div :class="['wraper', options.showfull == 1 ? '' : 'w', options.leftblank == 1 ? 'leftblank' : '']">
            <div :class="['content', options.radius]">
                <el-carousel :interval="5000" :height="options.height">
                    <el-carousel-item v-for="item in options.images" class="item"
                        :style="{ 'background-image': 'url(' + item.url + ')' }">
                        <p @click="redirect(item.link)" class="block w-full pointer"></p>
                    </el-carousel-item>
                </el-carousel>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { redirect } from '@/common/util.js'

const props = defineProps({
    options: {
        type: Object,
        default() {
            return {}
        }
    }
})

const loading = ref(true)
onMounted(() => {
    loading.value = false
})
</script>
<style scoped>
.swiperbox {
    background: var(--bgcolor);
    padding: var(--space) 0;
}

.swiperbox .item {
    background-size: auto 100%;
    background-position: center center;
    background-repeat: no-repeat;
}

.swiperbox .item p {
    height: var(--height);
}

.swiperbox .round .item {
    border-radius: 8px;
}

.swiperbox .leftblank .content {
    margin-left: calc(16.7% + 17px);
    margin-right: 10px;
}

.swiperbox .w.leftblank .content {
    margin-left: calc(16.7% + 10px);
    margin-right: 10px;
}
</style>

