<template>
    <div v-if="options.url" class="imagead"
        :style="{ '--bgcolor': options.bgcolor, '--height': options.height > 0 ? options.height + 'px' : '', '--margin': options.margin + 'px', '--space': options.space + 'px' }"
        v-loading="loading">
        <div :class="['wraper', options.showfull == 1 ? '' : 'w']">
            <div :class="['content', options.radius, options.link ? 'pointer' : '']">
                <img @click="redirect(options.link)" class="block w-full" :src="options.url">
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
.imagead {
    background: var(--bgcolor);
    padding: var(--space) 0;
    margin: 0 var(--margin);
}

.imagead .content img {
    height: var(--height);
    box-sizing: border-box;
}

.imagead .content.round img {
    border-radius: 6px;
}
</style>

