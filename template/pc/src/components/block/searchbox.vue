<template>
    <div class="searchbox"
        :style="{ '--bgcolor': options.bgcolor || '#ffffff', '--boxcolor': options.boxcolor || '#ffffff', '--buttoncolor': options.buttoncolor || '#e23435', '--txtcolor': options.txtcolor, '--space': (options.space || 30) + 'px' }"
        v-loading="loading">
        <div :class="['wraper', (options.showfull == 1 && route.path == '/') ? 'ml10 mr10' : 'w']">
            <div class="content uni-flex uni-row">
                <div class="lp">
                    <router-link to="/"><img :src="options.image || site.site_logo" class="block" /></router-link>
                </div>
                <div class="mp width-surplus">
                    <p class="uni-flex uni-row relative box">
                        <el-input v-model="form.keyword" :prefix-icon="Search" :placeholder="options.keyword || '搜索商品'"
                            class="pointer">
                            <template #append>
                                <p @click="search(form.keyword)" class="pl20 pr20">搜索</p>
                            </template>
                        </el-input>
                    </p>
                    <p class="f-12 mt10 line-clamp-1 pr10">
                        <label>热门搜索：</label>
                        <router-link :to="'/search/goods?keyword=' + item" class="rlink ml10 mr10"
                            v-for="item in site.keywords">{{ item }}</router-link>
                    </p>
                </div>
                <div class="rp">
                    <router-link to='/cart/index'
                        class="uni-flex uni-row f-13 cart pl10 pr5 flex-middle word-break-all rlink relative">
                        <el-icon :size="14" class="absolute">
                            <ShoppingCart />
                        </el-icon>
                        <span class="ml5">去购物车结算</span>
                        <span class="f-simsun ml5 mr5">></span>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { Search } from '@element-plus/icons-vue'
import { redirect } from '@/common/util.js'
import { siteRead } from '@/api/site.js'

const loading = ref(false)
const route = useRoute()
const form = reactive({ keyword: '' })
const site = reactive({ keywords: [] })

const props = defineProps({
    options: {
        type: Object,
        default() {
            return {}
        }
    }
})

onMounted(() => {
    Object.assign(form, { keyword: props.options.keyword }, route.query)
    siteRead(null, (data) => {
        site.keywords = data.hot_keywords
        site.site_logo = data.site_logo
    })
})

const search = (keyword) => {
    Object.assign(form, { keyword: keyword })
    redirect('/search/goods' + (form.keyword ? '?keyword=' + form.keyword : ''))
}
</script>
<style scoped>
.searchbox {
    background: var(--bgcolor);
    padding: var(--space) 0;
}

.searchbox .lp {
    width: 18%;
}

.searchbox .lp img {
    max-width: 100%;
    max-height: 60px;
}

.searchbox .mp {
    overflow: hidden;
    margin: 0 5%;
}

.searchbox .mp,
.searchbox .mp .rlink,
:deep() .el-icon,
:deep() .el-input__inner,
:deep() .el-input__inner::placeholder {
    color: var(--txtcolor);
}

.searchbox .mp .rlink:hover,
.searchbox .rp .cart:hover,
.searchbox .rp .cart:hover .el-icon {
    color: var(--buttoncolor);
}

.searchbox .mp .box {
    width: calc(100% - 2px);
    height: 32px;
    line-height: 32px;
    border: 1px var(--buttoncolor) solid;
    background-color: var(--boxcolor);
}

.searchbox .rp .cart {
    line-height: 32px;
    border: 1px #ddd solid;
    display: inline-block;
    background-color: var(--boxcolor);
    color: var(--txtcolor);
    padding-left: 22px;
}

.searchbox .rp .cart .absolute {
    left: 10px;
    top: 10px;
}

:deep() .el-input-group__append {
    background-color: var(--buttoncolor);
    color: #fff;
    box-shadow: none;
    border-radius: 0;
    padding: 0;
}

:deep() .el-input__inner {
    box-shadow: none;
    border-radius: 0;
    background-color: var(--boxcolor);
}

:deep() .el-input__inner::placeholder {
    font-size: 12px;
}
</style>

