<template>
    <div class="blankbox"
        :style="{ '--bgcolor': options.bgcolor, '--height': options.height + 'px', '--space': options.space + 'px' }" v-loading="loading">
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
.blankbox {
    background: var(--bgcolor);
    padding: var(--space) 0;
    height: var(--height);
}
</style>

