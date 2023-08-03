<template>
    <div class="textbox"
        :style="{ '--bgcolor': options.bgcolor, '--txtcolor': options.txtcolor || '#333333', '--fontsize': options.fontsize + 'px', '--txtbold': options.txtbold == 1 ? 'bold' : 'normal', '--space': options.space + 'px' }"
        v-loading="loading">
        <div :class="['wraper center', options.showfull == 1 ? 'ml10 mr10' : 'w']">
            <text class="inline-block f-14 text">{{ options.label || '文本' }}</text>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue'

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
.textbox {
    background: var(--bgcolor);
    padding: var(--space) 0;
}

.textbox text {
    color: var(--txtcolor);
    font-weight: var(--txtbold);
    font-size: var(--fontsize);
}
</style>

