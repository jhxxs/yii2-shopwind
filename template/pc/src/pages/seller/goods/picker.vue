<template>
    <myhead></myhead>
    <div class="main w pt10">
        <el-row :gutter="12">
            <el-col :span="4">
                <menus></menus>
            </el-col>
            <el-col :span="20">
                <div class="round-edge pd10 bgf">
                    <div class="pd10">
                        <el-breadcrumb separator="/">
                            <el-breadcrumb-item>商品</el-breadcrumb-item>
                            <el-breadcrumb-item>采集商品</el-breadcrumb-item>
                        </el-breadcrumb>
                    </div>
                </div>
                <div class="round-edge pd10 bgf mt20">
                    <div class="pl10 pt10">
                        <el-form>
                            <el-form-item label="商品类目">
                                <multiselector api="category/list" idField="cate_id" nameField="cate_name"
                                    parentField="parent_id" :placeholder="false" @callback="callback">
                                </multiselector>
                            </el-form-item>
                            <el-form-item label="商品链接">
                                <el-input @click="selection" v-model="form.urls" @blur="format" type="textarea"
                                    resize="none" rows="10" class="textarea" :readonly="form.code == 'xyb2b'" />
                                <p class="f-gray f-12 w-full">注：每个链接放一行</p>
                            </el-form-item>
                            <el-form-item label="采集平台">
                                <el-select @change="change" v-if="platforms.length > 0" v-model="form.code">
                                    <el-option v-for="item in platforms" :label="item.name" :value="item.code" />
                                </el-select>
                                <el-tag v-else size="large">采集插件未配置</el-tag>
                            </el-form-item>
                            <el-form-item v-if="summary" label=" " :label-width="68">
                                <p class="f-gray l-h17 f-12 textarea">{{ summary }}</p>
                            </el-form-item>
                            <el-form-item :label-width="68">
                                <el-button @click="submit" type="primary" class="mt20">
                                    <text class="pl10 pr10">开始采集</text>
                                </el-button>
                            </el-form-item>
                        </el-form>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>

    <selector title="选择商品" :visible="dialogSelector" :limit="10" @close="dialogClose">
    </selector>
    <dialogpicker title="采集商品" :visible="dialogVisible" :data="dialogData" @close="dialogClose">
    </dialogpicker>

    <myfoot></myfoot>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { unique } from '@/common/util.js'
import { pickerList } from '@/api/picker.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'
import multiselector from '@/components/selector/multiselector.vue'
import dialogpicker from '@/components/dialog/goods/picker.vue'
import selector from '@/components/dialog/goods/pickerlist.vue'

const form = reactive({ urls: '', code: '' })
const dialogVisible = ref(false)
const dialogSelector = ref(false)
const dialogData = ref([])
const platforms = ref([])
const summary = ref('')

onMounted(() => {
    pickerList(null, (data) => {
        platforms.value = data
        if (data.length > 0) {
            form.code = data[0].code
            summary.value = data[0].summary
        }
    })
})

const format = () => {
    let array = form.urls.trim().length > 0 ? form.urls.trim().split('\n') : []
    for (let i = 0; i < array.length; i++) {
        array[i] = array[i].trim()
    }
    form.urls = unique(array).join('\n')
}
const callback = (value) => {
    form.cate_id = value.id
}
const change = (value) => {
    platforms.value.forEach((item) => {
        if (item.code == value) {
            summary.value = item.summary
        }
    })
}
const selection = () => {
    if (form.code == 'xyb2b') {
        dialogSelector.value = true
    }
}
const submit = () => {
    if (form.urls.length > 0 && form.code) {
        dialogVisible.value = true
        dialogData.value = Object.assign({}, form, { urls: form.urls.split('\n') })
    } else {
        ElMessage.warning('商品链接及商品来源不能为空')
    }
}
const dialogClose = (value) => {
    dialogVisible.value = false
    dialogSelector.value = false

    if (value && form.code == 'xyb2b') {
        let urls = []
        unique(value).forEach((item) => {
            // 虚假链接，为了兼容接口，只为获取ID
            urls.push('https://www.xyb2b.com/buyer/mall/goodDetail?skuId=' + item)
        })
        form.urls = urls.join('\n')
    }
}
</script>
<style scoped>
.textarea {
    width: 600px;
}
</style>
