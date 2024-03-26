<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="600" :center="true" :draggable="true"
        :destroy-on-close="true" :close-on-click-modal="false" :before-close="close">
        <el-form :inline="true">
            <el-form-item label="分类名称" :label-width="85">
                <el-input v-model="category.cate_name" clearable />
            </el-form-item>
            <el-form-item label="上级分类" :label-width="85">
                <el-select v-model="category.parent_id">
                    <el-option label="请选择" :value="0" />
                    <el-option v-for="item in options" :label="item.cate_name" :value="parseInt(item.cate_id)" />
                </el-select>
            </el-form-item>
            <el-form-item label="排序" :label-width="85" clearable>
                <el-input v-model="category.sort_order" placeholder="255" />
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="mb20">
                <el-button @click="close">关闭</el-button>
                <el-button type="primary" @click="submit" :loading="loading">保存</el-button>
            </div>
        </template>
    </el-dialog>
</template>
<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { categoryAdd, categoryUpdate, categoryList } from '@/api/category.js'

const props = defineProps({
    title: { type: String, default: '' },
    visible: { type: Boolean, default: false },
    data: {
        type: [Object, Array],
        default: () => {
            return {}
        }
    }
})

const dialogVisible = ref(false)
const loading = ref(false)
const category = ref({})
const options = ref([])

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})
watch(() => props.data, (value) => {
    if (value.parent_id) value.parent_id = parseInt(value.parent_id);
    category.value = value
})

onMounted(() => {
    let visitor = JSON.parse(localStorage.getItem('visitor'))

    categoryList({ store_id: visitor.store_id, parent_id: 0, page_size: 50 }, (data) => {
        options.value = data.list
    })
})

const emit = defineEmits(['close'])
const submit = () => {
    if (category.value.cate_id) {
        categoryUpdate(category.value, (data) => {
            if (data) {
                ElMessage.success('编辑成功')
                emit('close', Object.assign(category.value, data))
            }
        }, loading)
    } else {
        categoryAdd(category.value, (data) => {
            if (data) {
                ElMessage.success('添加成功')
                emit('close', Object.assign(category.value, data))
            }
        }, loading)
    }
}
const close = () => {
    emit('close', null)
}

</script>
<style scoped>
.el-form {
    margin: 0 100px;
}

:deep() .el-select {
    margin-bottom: 10px;
}
</style>