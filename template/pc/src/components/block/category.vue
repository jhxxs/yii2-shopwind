<template>
    <div class="categorybox"
        :style="{ '--bgcolor': options.bgcolor || '#ffffff', '--catebgcolor': options.catebgcolor || '#e23435', '--catebodybgcolor': options.catebodybgcolor || 'rgba(0, 0, 0, 0.6)', '--catetxtcolor': options.catetxtcolor || '#ffffff', '--selcolor': options.selcolor || '#e23435', '--txtcolor': options.txtcolor, '--w': width + 'px', '--width': categories.width + 'px', '--height': categories.height + 'px', '--space': options.space + 'px' }"
        v-loading="loading">
        <div ref="wraper" :class="['wraper relative', (options.showfull == 1 && route.path == '/') ? '' : 'w']">
            <ul class="content flex-middle line-clamp-1">
                <li class="item inline-block pt10 pb10 f-16 all center">
                    <span @click="redirect(options.cate_id > 0 ? '/search/goods/' + options.cate_id : '/category/goods')"
                        class="word-break-all pointer">
                        {{ options.cate_name || '所有商品' }}分类</span>
                </li>
                <li v-for="(item, key) in options.navs"
                    :class="['item inline-block ml20 mr20 pt10 pb10 f-16', (route.fullPath == item.link || route.fullPath == '/' + item.link) ? 'selected' : '']">
                    <span @click="redirect(item.link)" class="pointer rlink">{{ item.title }}</span>
                </li>
            </ul>
            <div ref="category"
                :class="['categories absolute', (options.expand == 1 && (route.path == '/' || route.path.indexOf('/channel/page/') > -1)) ? 'block' : 'hidden']">

                <div v-for="(item, key) in categories.list">
                    <div v-if="Number(options.rows) <= 0 || (Number(options.rows) > key)"
                        class="uni-flex uni-row flex-middle width-between item">
                        <div class="uni-flex uni-column width-surplus f-white">
                            <router-link :to="'/search/goods/' + item.id" class="f-white line-clamp-1 rlink">
                                {{ item.value }}
                            </router-link>
                            <p :class="['l-h20 f-13 mr5 mt5', options.cate_id > 0 ? 'line-clamp-2' : 'line-clamp-1']">
                                <span v-for="(child, index) in Object.values(item.children)" class="inline-block">
                                    <router-link :to="'/search/goods/' + child.id"
                                        v-if="index < (options.cate_id > 0 ? 6 : 3)"
                                        class="rlink f-13 f-white mr10 mt5 word-break-all inline-block">
                                        {{ child.value }}
                                    </router-link>
                                </span>
                            </p>
                        </div>
                        <el-icon color="#ffffff">
                            <ArrowRight />
                        </el-icon>

                        <div class="popover bgf absolute hidden">
                            <div class="bd">
                                <div v-if="!(options.cate_id > 0)" class="uni-flex uni-row badge mb10 pb5">
                                    <router-link v-for="(child) in Object.values(item.children)"
                                        :to="'/search/goods/' + child.id"
                                        class="uni-flex uni-row vertical-middle rlink f-12 mr20 f-white pr5">
                                        <span class="pl10">{{ child.value }}</span>
                                        <el-icon :size="12">
                                            <ArrowRight />
                                        </el-icon>
                                    </router-link>
                                </div>
                                <div v-if="options.cate_id > 0">
                                    <el-row class="mb10 f-12">
                                        <el-col :span="24" class="uni-flex uni-row flex-wrap">
                                            <router-link v-for="(child) in Object.values(item.children)"
                                                :to="'/search/goods/' + child.id"
                                                class="rlink ml10 mr10 word-break-all inline-block">
                                                {{ child.value }}
                                            </router-link>
                                        </el-col>
                                    </el-row>
                                </div>
                                <div v-else>
                                    <el-row v-for="(child) in Object.values(item.children)" class="mb10 f-12">
                                        <el-col :span="2">
                                            <router-link :to="'/search/goods/' + child.id"
                                                class="uni-flex uni-row rlink bold vertical-middle">
                                                <span class="width-surplus text-right line-clamp-1">
                                                    {{ child.value }}</span>
                                                <el-icon class="ml5 mr5">
                                                    <ArrowRight />
                                                </el-icon></router-link>
                                        </el-col>
                                        <el-col :span="22">
                                            <router-link :to="'/search/goods/' + child2.id"
                                                v-for="(child2) in Object.values(child.children)"
                                                class="rlink mr20 f-13 word-break-all inline-block">
                                                {{ child2.value }}</router-link>
                                        </el-col>
                                    </el-row>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch, getCurrentInstance } from 'vue'
import { useRoute } from 'vue-router'
import { redirect } from '@/common/util.js'
import { categoryTree } from '@/api/category.js'

const route = useRoute()
const categories = reactive({ list: [], width: 0, height: 0 })
const width = ref(0)

const props = defineProps({
    options: {
        type: Object,
        default() {
            return {}
        }
    }
})

const loading = ref(false)
const { proxy } = getCurrentInstance()
onMounted(() => {

    let parent_id = parseInt(props.options.cate_id)
    let cachekey = parent_id > 0 ? parent_id : ''
    let categorys = JSON.parse(localStorage.getItem('categories' + cachekey)) || []

    if (categorys.length > 0) {
        loading.value = false
        categories.list = categorys
    }
    else {
        categoryTree({ layer: 3, parent_id: parent_id }, (data) => {
            categories.list = Object.values(data)
            localStorage.setItem('categories' + cachekey, JSON.stringify(categories.list))
        }, loading)
    }
})

watch(() => categories.list, (value) => {
    proxy.$nextTick(() => {
        width.value = proxy.$refs.wraper.clientWidth
        categories.width = proxy.$refs.category.clientWidth
        categories.height = proxy.$refs.category.clientHeight
    })
})

</script>
<style scoped>
.categorybox {
    background: var(--bgcolor);
    padding: var(--space) 0;
}

.categorybox .wraper {
    z-index: 9;
}

.categorybox .content {
    width: 100%;
    margin-right: 10px;
    overflow: hidden;
}

.categorybox .content .item {
    color: var(--txtcolor);
}

.categorybox .content .item.selected .rlink {
    color: var(--selcolor);
}

.categorybox .item.all {
    color: var(--catetxtcolor);
    background-color: var(--catebgcolor);
}

.categorybox .item.all,
.categorybox .categories {
    width: 16.67%;
}

.categorybox .categories {
    z-index: 1;
    top: 41px;
    left: 0;
    background-color: var(--catebodybgcolor);
}

.categories .item {
    padding: 12px 8px 12px 15px;
}

.categories .item:hover {
    background-color: var(--catebodybgcolor);
}

.categories .item p span .rlink {
    opacity: 0.6;
}

.categories .item:hover .popover {
    display: block;
}

.categories .popover {
    left: calc(var(--width) + 0.2px);
    top: 0;
    width: calc(var(--w) - var(--width));
    height: var(--height);
    line-height: 25px;
    overflow: hidden;
}

.categories .popover .bd {
    width: calc(100% - 41px);
    height: calc(100% - 41.5px);
    border: 1px var(--catebodybgcolor) solid;
    border-left: 0;
    padding: 20px;
}

.categories .popover .badge .rlink {
    background-color: var(--catebodybgcolor);
}

.categories .popover .badge .rlink:hover {
    color: #fff;
    background-color: var(--my-color-red);
}
</style>

