<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="600" :center="true" :draggable="true" :destroy-on-close="true"
        :close-on-click-modal="false" :before-close="close">
        <div v-if="gallery.length > 0" class="flex-center pb20">
            <p v-if="!file">文件创建中...</p>
            <p v-else class="mb10">文件创建成功：<a @click="close" :href="file" class="f-blue rlink">点此下载</a></p>
        </div>
    </el-dialog>
</template>
<script setup>
import { ref, reactive, watch } from 'vue'
import { orderExport } from '@/api/order.js'

const props = defineProps({
    title: { type: String, default: '' },
    visible: { type: Boolean, default: false },
    data: {
        type: [Object, Array],
        default: () => {
            return []
        }
    }
})

const dialogVisible = ref(false)
const gallery = ref(props.data)
const file = ref()

watch(() => props.visible, (value) => {
    dialogVisible.value = value

    if (value) {
        let items = []
        gallery.value.forEach((item) => {
            items.push(item.order_id)
        })
        orderExport({ items: items }, (data) => {
            file.value = data
        })
    }
})

watch(() => props.data, (value) => {
    gallery.value = value
})

const emit = defineEmits(['close'])
const close = () => {
    emit('close', null)
}

</script>