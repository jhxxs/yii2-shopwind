<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="1100" :center="true" :draggable="true"
        :destroy-on-close="true" :close-on-click-modal="false" :before-close="close">
        <div class="pl10 pt10">
            <el-form :inline="true">
                <el-form-item label="商品类目">
                    <multiselector api="category/list" idField="cate_id" nameField="cate_name" parentField="parent_id"
                        :placeholder="true" @callback="callback">
                    </multiselector>
                </el-form-item>
                <el-form-item label="推荐状态">
                    <el-select v-model="form.recommended" @change="queryClick" placeholder="不限制" clearable>
                        <el-option label="已推荐的商品" value="1" />
                        <el-option label="未推荐的商品" value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item label="商品排序">
                    <el-select v-model="form.orderby" @change="queryClick" placeholder="不限制" clearable>
                        <el-option label="销量从高到低" value="sales|desc" />
                        <el-option label="点击量从高到低" value="views|desc" />
                        <el-option label="价格从高到低" value="price|desc" />
                        <el-option label="价格从低到高" value="price|asc" />
                        <el-option label="上架时间从近到远" value="add_time|desc" />
                        <el-option label="上架时间从远到近" value="add_time|asc" />
                        <el-option label="评论数从多到少" value="comments|desc" />
                    </el-select>
                </el-form-item>
                <el-form-item label="商品名称">
                    <el-input v-model="form.keyword" clearable />
                </el-form-item>
                <el-form-item label="品牌">
                    <el-input v-model="form.brand" clearable />
                </el-form-item>
                <el-form-item>
                    <el-button @click="queryClick" type="primary" class="f-13">查询</el-button>
                </el-form-item>
            </el-form>
        </div>
        <div v-loading="loading">
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
                <el-table-column prop="sales" label="销量" width="80" align="center" sortable />
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
            <el-button type="primary" @click="submit" :loading="loading">确定</el-button>
        </template>
    </el-dialog>
</template>
<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { currency, unique } from '@/common/util.js'
import { goodsList } from '@/api/goods.js'
import { getCurrentInstance } from '@vue/runtime-core'
import multiselector from '@/components/selector/multiselector.vue'

const props = defineProps({
    title: { type: String, default: '' },
    visible: { type: Boolean, default: false },
    data: {
        type: [Object, Array],
        default: () => {
            return {}
        }
    },
    limit: {
        type: Number,
        default: 0
    },
    selected: {
        type: Array,
        default: () => {
            return []
        }
    }
})

const dialogVisible = ref(false)
const loading = ref(false)
const gallery = ref([])
const pagination = ref({})
const form = reactive({ selected: [] })
const { proxy } = getCurrentInstance()

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})
watch(() => props.selected, (value) => {
    form.selected = value || []
})

const handleCurrentChange = (value) => {
    getList({ page: value, page_size: pagination.value.page_size })
}

const queryClick = () => {
    getList()
}
const callback = (value) => {
    form.cate_id = value.id
    getList()
}

const selectClick = (selection, row) => {
    let array = []
    selection.forEach((item) => {
        array.push(parseInt(item.goods_id))
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
        gallery.value.forEach((item) => {
            let value = parseInt(item.goods_id)
            if (form.selected.indexOf(value) > -1) {
                form.selected.splice(form.selected.indexOf(value), 1)
            }
        })
    } else {
        selection.forEach((item) => {
            form.selected.push(parseInt(item.goods_id))
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
    let visitor = JSON.parse(localStorage.getItem('visitor')) || {}
    goodsList(Object.assign({ store_id: visitor.store_id, page_size: 4 }, form, params), (data) => {
        gallery.value = data.list
        pagination.value = data.pagination
        
        proxy.$nextTick(() => {
            gallery.value.forEach((row) => {
                if (form.selected && form.selected.indexOf(parseInt(row.goods_id)) > -1) {
                    proxy.$refs.multipleTableRef.toggleRowSelection(row, true)
                }
            })
        })
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