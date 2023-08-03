<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="1000" :center="true" :draggable="true"
        :destroy-on-close="true" :close-on-click-modal="false" :before-close="close">
        <div v-loading="loading">
            <el-form :inline="true" class="ml10">
                <el-form-item label="商品类目">
                    <el-select v-for="(options, column) in categorys" v-model="multiIndex[column]"
                        @change="change($event, column)" class="mr10 mb10">
                        <el-option label="请选择" :value="-1" />
                        <el-option v-for="(item, index) in options" :label="item.name" :value="index" />
                    </el-select>
                </el-form-item>
            </el-form>
            <el-table ref="multipleTableRef" :data="gallery" :border="false" :stripe="false" @select="selectClick"
                @selectAll="selectAllClick">
                <el-table-column fixed="left" type="selection" />
                <el-table-column width="70" label="图片" align="center">
                    <template #default="scope">
                        <img :src="scope.row.default_image" width="50" height="50" />
                    </template>
                </el-table-column>
                <el-table-column label="商品名称" width="300">
                    <template #default="scope">
                        <p class="l-h17">{{ (scope.row.goods_name) }}</p>
                    </template>
                </el-table-column>
                <el-table-column prop="category" label="所在分类" width="220">
                    <template #default="scope">
                        <p v-if="scope.row.category" class="l-h17">{{ (scope.row.category).join(' / ') }}</p>
                    </template>
                </el-table-column>
                <el-table-column prop="price" label="价格" width="150" align="center">
                    <template #default="scope">
                        <p class="f-yahei">{{ currency(scope.row.price) }}</p>
                    </template>
                </el-table-column>
                <el-table-column prop="stocks" label="库存" width="80" sortable />
                <el-table-column prop="brand" label="品牌" width="100" sortable />
            </el-table>
            <div v-if="pagination.total > 0" class="mt20">
                <el-pagination v-model:currentPage="pagination.page" v-model:page-size="pagination.page_size"
                    :background="false" layout="total, prev, pager, next" :total="pagination.total"
                    @current-change="handleCurrentChange" :hide-on-single-page="false" class="center" />
            </div>
        </div>
        <template #footer>
            <el-button @click="close">关闭</el-button>
            <el-button type="primary" @click="submit">确定</el-button>
        </template>
    </el-dialog>
</template>
<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { currency, unique } from '@/common/util.js'
import { pickerGoods, pickerCategory } from '@/api/picker.js'
import { getCurrentInstance } from '@vue/runtime-core'

const props = defineProps({
    title: { type: String, default: '' },
    visible: { type: Boolean, default: false },
    limit: {
        type: Number,
        default: 10
    }
})

const dialogVisible = ref(false)
const loading = ref(false)
const gallery = ref([])
const categorys = ref([])
const pagination = ref({ total: 0 })
const form = reactive({ selected: [], code: 'xyb2b' })
const multiIndex = reactive([-1, -1, -1, -1])
const { proxy } = getCurrentInstance()

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})

const handleCurrentChange = (value) => {
    getList({ page: value })
}

onMounted(() => {
    getList()
    getCategorys()
})

const change = (value, column) => {
    if (value > -1) {
        multiIndex[column] = value
        form.categoryId = categorys.value[column][value].id
        getCategorys(form.categoryId, column + 1)
    } else {
        for (let index = column; index < multiIndex.length; ++index) {
            multiIndex[index] = -1
        }
        categorys.value.splice(column + 1)
        if (column == 0) form.categoryId = 0
        else form.categoryId = categorys.value[column - 1][multiIndex[column - 1]].id
    }
    getList()
}
const selectClick = (selection, row) => {
    let array = []
    selection.forEach((row) => {
        array.push(parseInt(row.goods_id))
    })
    let value = parseInt(row.goods_id)
    if (array.indexOf(value) > -1) {
        form.selected.push(value)
    } else if (form.selected.indexOf(value) > -1) {
        form.selected.splice(form.selected.indexOf(value), 1)
    }
    form.selected = getValues()
}
const selectAllClick = (selection) => {
    if (selection.length == 0) {
        gallery.value.forEach((row) => {
            let value = parseInt(row.goods_id)
            if (form.selected.indexOf(value) > -1) {
                form.selected.splice(form.selected.indexOf(value), 1)
            }
        })
    } else {
        selection.forEach((row) => {
            form.selected.push(parseInt(row.goods_id))
        })
    }
    form.selected = getValues()
}

const emit = defineEmits(['close'])
const close = () => {
    emit('close', null)
}
const submit = () => {
    emit('close', form.selected)
}

function getList(params) {
    pickerGoods(Object.assign({ page_size: 4 }, form, params), (data) => {
        gallery.value = data.list || []
        pagination.value = data.pagination || {}

        proxy.$nextTick(() => {
            gallery.value.forEach((row) => {
                if (form.selected && form.selected.indexOf(parseInt(row.goods_id)) > -1) {
                    proxy.$refs.multipleTableRef.toggleRowSelection(row, true)
                }
            })
        })
    }, loading)
}
function getCategorys(categoryId = 0, column = 0) {
    pickerCategory({ categoryId: categoryId, code: form.code }, (data) => {
        if (categorys.value.length > column) {
            categorys.value.splice(column)
            for (let index = column; index < multiIndex.length; ++index) {
                multiIndex[index] = -1
            }
        }
        categorys.value[categorys.value.length > 0 ? column : 0] = data
    })
}
function getValues() {
    if (props.limit > 0) {
        form.selected = form.selected.splice(-props.limit)
    }
    return unique(form.selected)
}

</script>
<style scoped>
.el-table,
.el-form-item {
    font-size: 13px;
}

:deep() .el-table__header-wrapper .el-table-column--selection .el-checkbox {
    vertical-align: middle;
}
</style>