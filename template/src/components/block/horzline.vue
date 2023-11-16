<template>
    <div class="horzline"
        :style="{ '--bgcolor': options.bgcolor, '--color': options.color || '#0d6fb8', '--line': options.line || 'solid', '--margin': options.margin + 'px', '--space': options.space + 'px' }"
        v-loading="loading">
        <div :class="['wraper', options.showfull == 1 ? 'ml10 mr10' : 'w']">
            <div class="content"></div>
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

const loading = ref(false)
onMounted(() => {
    loading.value = false
})

</script>
<style scoped>
.horzline {
    background: var(--bgcolor);
    padding: var(--space) 0;
}

.horzline .content {
    margin: 0 var(--margin);
    border-top-width: 1px;
    border-top-style: var(--line);
    border-top-color: var(--color);
}
</style>

