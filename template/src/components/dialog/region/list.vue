<template>
    <el-dialog v-model="dialogVisible" :width="700" :title="title" :center="true" :draggable="true"
        :destroy-on-close="true" :close-on-click-modal="false" :before-close="close">
        <div class="uni-flex uni-row gallery">
            <el-scrollbar v-for="(list, layer) in regions.list" :height="300" class="list">

                <div v-for="(item, index) in list"
                    :class="['uni-flex uni-row width-between  flex-middle pl10 pr10', item.current ? 'current' : '']">
                    <el-checkbox @change="(value) => {queryClick(layer, item)}" v-model="item.checked"
                        :label="item.region_id" :indeterminate="item.indeterminate" :disabled="item.disabled">
                        {{item.region_name}}</el-checkbox>
                    <p @click="queryClick(layer, item)" class="width-surplus" style="text-align:right">
                        <el-icon v-if="layer == 0">
                            <ArrowRight />
                        </el-icon>
                    </p>
                </div>

            </el-scrollbar>
        </div>
        <template #footer>
            <div class="mb20">
                <el-button @click="close">关闭</el-button>
                <el-button type="primary" @click="submit" :loading="loading">提交</el-button>
            </div>
        </template>
    </el-dialog>
</template>
<script setup>
import { onMounted, ref, reactive, watch } from 'vue'
import { regionList } from '@/api/region.js'

const props = defineProps({
    title: { type: String, default: '' },
    visible: { type: Boolean, default: false },
    data: {
        type: [Object, Array],
        default: () => {
            return {}
        }
    },
    original: { type: String, default: '' }
})

const dialogVisible = ref(false)
const loading = ref(false)
const delivery = reactive({ original: [] })
const regions = reactive({ list: [], all: {} })

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})

watch(() => props.data, (value) => {

})
watch(() => props.original, (value) => {

    // if (value) {
    //     value.split('|').forEach((item, index) => {
    //         delivery.original[index] = parseInt(item)
    //     })
    // }

    // if (delivery.original.length > 0) {
    //     regions.list.forEach((list, layer) => {
    //         list.forEach((item) => {
    //             if (delivery.original.indexOf(item.region_id) > -1) {
    //                 item.checked = true
    //                 if (layer > 0) {
    //                     for (let i; i < regions.list[layer - 1].length; i++) {
    //                         if (regions.list[layer - 1].cate_id == item.parent_id) {
    //                             regions.list[layer - 1].indeterminate = true
    //                             break
    //                         }
    //                     }
    //                 }
    //             }
    //         })
    //     })
    // }
})

onMounted(() => {
    regionList({ parent_id: 0, page_size: 100 }, (data) => {
        if (data.list.length > 0) {
            data.list[0].current = true
            regions.list.push(data.list)

            let parent = data.list[0]
            regions.all[parent.region_id] = parent
            regionList({ parent_id: parent.region_id, page_size: 100 }, (child) => {
                regions.list.push(child.list)
                regions.all[parent.region_id].children = child.list
            })
        }
    })
})

const emit = defineEmits(['close'])
const submit = () => {
    dialogVisible.value = false
    emit('close', getValues())
}
const close = () => {
    dialogVisible.value = false
    emit('close', null)
}

const queryClick = (layer, item) => {
    if (layer > 0) parentHandle(layer)
    else getList(layer, item)
}

const parentHandle = (layer) => {
    let checked = []
    regions.list[layer].forEach((region) => {
        if (region.checked) checked.push(region)
    })

    regions.list[layer - 1].forEach((parent) => {
        if (parent.current) {
            if (checked.length == 0) {
                parent.checked = false
                parent.indeterminate = false
            } else if (checked.length == regions.list[layer].length) {
                parent.checked = true
                parent.indeterminate = false
            } else {
                parent.indeterminate = true
            }
        }
    })
}

function getList(layer, item, callback) {
    item.indeterminate = false
    regions.list[layer].forEach((region) => {
        region.current = (region == item) ? true : false
    })
    regionList({ parent_id: item.region_id, page_size: 100 }, (data) => {
        item.children = data.list
        regions.all[item.region_id] = item

        if (data.list.length > 0) {
            data.list.forEach((find) => {
                find.checked = item.checked ? true : false
            })

            if (typeof callback == "function") {
                callback(data.list)
            } else regions.list[layer + 1] = data.list
        }
    })
}

function getValues() {
    let list = [[], []]
    Object.values(regions.all).forEach((item) => {
        if (!item.disabled) {
            if (item.checked && !item.indeterminate) {
                list[0].push(item.region_name)
                list[1].push(parseInt(item.region_id))
            } else if (item.indeterminate) {
                item.children.forEach((child) => {
                    if (child.checked) {
                        list[0].push(child.region_name)
                        list[1].push(parseInt(child.region_id))
                    }
                })
            }
        }
    })

    return list
}

</script>
<style scoped>
.gallery {
    border: 1px #eee solid;
    border-radius: 4px;
}

.gallery .el-scrollbar {
    width: 50%;
}

.gallery .list {
    border-right: 1px #eee solid;
}

.gallery .list:last-child {
    border-right: 0;
}

.gallery .list .current {
    background-color: #f1f1f1;
}
</style>