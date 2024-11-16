<template>
    <div v-for="(options, column) in result.multiList" :key="column" class="warper">
        <el-select v-if="options.length > 0" v-model="multiIndex[column]" @change="change($event, column)" class="mr10">
            <el-option v-if="props.placeholder" label="请选择" :value="-1" />
            <el-option v-for="(item, index) in options" :key="index" :label="item[props.nameField]" :value="index" />
        </el-select>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { build } from '@/common/selector.js'

const props = defineProps({
    api: {
        type: String,
        default: 'region/list'
    },
    data: {
        type: Object,
        default: () => {
            return {}
        }
    },
    original: {
        type: Array,
        default: () => {
            return []
        }
    },
    idField: {
        type: String,
        default: 'region_id'
    },
    parentField: {
        type: String,
        default: 'parent_id'
    },
    nameField: {
        type: String,
        default: 'name'
    },
    placeholder: {
        type: Boolean,
        default: false
    }
})

const result = ref({})
const multiIndex = reactive(props.placeholder ? [-1, -1, -1, -1] : [0, 0, 0, 0])

onMounted(() => {
    if (props.data.multiList) {
        result.value = props.data
    } else {
        getList(props.original)
    }
})

watch(() => props.data, (value) => {
    result.value = value
})

watch(() => props.original, (value) => {
    getList(value)
})

const change = (value, column) => {
    multiIndex[column] = value
    for (let index = column + 1; index <= result.value.multiList.length; index++) {
        multiIndex[index] = props.placeholder ? -1 : 0
    }
    getList()
}

const emit = defineEmits(['callback'])
const getList = (original) => {
    build(multiIndex, original, props.api, props.idField, props.nameField, props.parentField).then((data) => {
        result.value = data
        emit('callback', { id: data.id, label: data.label })
    })
}
</script>
<style scoped>
.warper:last-child .el-select {
    margin-right: 0
}
</style>

