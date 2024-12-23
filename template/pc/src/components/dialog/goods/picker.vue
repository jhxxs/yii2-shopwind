<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="500" :center="true" :draggable="true"
        :destroy-on-close="false" :close-on-click-modal="false" :show-close="progress.total == progress.number"
        :before-close="close">
        <div class="mb20 pb20 center">
            <p v-if="progress.total > progress.number">
                正在采集第<span class="f-green">{{ progress.number + 1 }}</span>
                / {{ progress.total }}个商品
            </p>
            <p v-else>
                商品采集完毕，
                成功<span class="f-green">{{ progress.success }}</span>个，
                失败<span class="f-red">{{ progress.fail }}</span>个
                <router-link to="/seller/goods/list" class="rlink f-red ml10">查看采集的商品</router-link>
            </p>
        </div>
    </el-dialog>
</template>
<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { pickerAdd } from '@/api/picker.js'

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
const progress = reactive({ total: 0, number: 0, success: 0, fail: 0 })

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})
watch(() => props.data, (value) => {
    progress.total = value.urls.length
    progress.number = 0
    progress.success = 0
    progress.fail = 0

    if (progress.total > 0) {

        // 延迟2秒执行
        setTimeout(() => {
            picker(value)
        }, 1000)
    }
})

const emit = defineEmits(['close'])
const close = () => {
    emit('close')
}

function picker(params) {
    let url = params.urls[0].trim()
    pickerAdd({ url: url, cate_id: params.cate_id, code: params.code }, (res) => {
        if (res.code == 0) progress.success++
        else progress.fail++

        // 移除该商品
        params.urls.splice(params.urls.indexOf(url), 1)

        // 继续下一个
        progress.number++
        if (params.urls.length > 0) {
            picker(params)
        } else {
            setTimeout(() => {
                emit('close')
            }, 10000);
        }
    })
}
</script>