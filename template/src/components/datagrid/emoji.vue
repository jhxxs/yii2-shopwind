<template>
    <div class="uni-flow l-h20">
        <span v-for="item in gallery" @click="selected" class="mb5 ml5 inline-block pointer">{{ item }}</span>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue'

const gallery = ref([
    '😃',
    '😄',
    '😁',
    '😭',
    '😆',
    '😅',
    '🤣',
    '😂',
    '😷',
    '😛',
    '🙂',
    '🙃',
    '😉',
    '😊',
    '😇',
    '🥰',
    '😍',
    '🤩',
    '😘',
    '😗',
    '😚',
    '🤑',
    '🥴',
    '😵'
])

const emit = defineEmits(['click'])
const selected = (e) => {
    emit('click', e.target)
}
</script>
