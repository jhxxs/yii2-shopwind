<template>
    <div class="goodslist" :style="{ '--bgcolor': options.bgcolor, '--space': options.space + 'px' }" v-loading="loading">
        <div :class="['wraper', options.showfull == 1 ? 'ml10 mr10' : 'w']">

            <div v-if="options.title || options.moreurl" class="title uni-flex uni-row width-between pt10 pb20">
                <p class="f-18 bold width-surplus">{{ options.title }}</p>
                <p v-if="options.moreurl" @click="redirect(options.moreurl)"
                    class="more pl10 pr5 pt5 pb5 f-gray bgf vertical-middle pointer rlink">
                    <span>更多</span>
                    <el-icon>
                        <ArrowRight />
                    </el-icon>
                </p>
            </div>

            <div
                :class="['content uni-flex uni-row flex-wrap', (options.images && options.images.length > 0) ? 'hasimg' : 'noimg']">

                <div v-if="options.images && options.images.length > 0" class="item scroller relative mr20 mb20">
                    <el-carousel :interval="5000" :height="height">
                        <el-carousel-item v-for="item in options.images">
                            <img @click="redirect(item.link)" :src="item.url" class="block w-full pointer" />
                        </el-carousel-item>
                    </el-carousel>
                </div>

                <div v-for="item in gallery" ref="goods" class="bgf center item mr20 mb20 pd15">
                    <router-link :to="'/goods/detail/' + item.goods_id" class="pic rlink">
                        <img @load="onload" :src="item.default_image" class="w-full block" />
                    </router-link>
                    <router-link :to="'/goods/detail/' + item.goods_id" class="desc line-clamp-2 mt10 rlink">
                        {{ item.goods_name }}
                    </router-link>
                    <p class="price mt10 line-clamp-1 f-16">
                        <span class="f-red">{{ currency(item.price) }}</span>
                        <del v-if="item.mkprice > 0" class="f-gray ml10">{{ currency(item.mkprice) }}</del>
						<del v-else class="f-gray ml10">{{ currency(item.price * 1.5) }}</del>
                    </p>
                    <p class="mt10 f-13 f-gray line-clamp-1">{{ item.sales || 0 }}人已付款</p>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch, getCurrentInstance } from 'vue'
import { goodsList } from '@/api/goods.js'
import { currency, redirect } from '@/common/util.js'

const props = defineProps({
    options: {
        type: Object,
        default() {
            return {}
        }
    }
})

const { proxy } = getCurrentInstance()
const loading = ref(true)
const gallery = ref([])
const height = ref('')
onMounted(() => {
    goodsList({ items: props.options.source == 'choice' ? props.options.items : '', orderby: props.options.orderby || '', cate_id: props.options.source == 'category' ? props.options.cate_id : 0, page_size: props.options.quantity }, (data) => {
        gallery.value = data.list
    }, loading)
})

const onload = () => {
    if (gallery.value.length > 0) {
        if (proxy.$refs.goods[0]) {
            height.value = proxy.$refs.goods[0].clientHeight + 'px'
        }
    }
}

</script>
<style scoped>
.goodslist {
    background: var(--bgcolor);
    padding: var(--space) 0;
}

.goodslist .item {
    width: calc(20% - 46px);
    border-radius: 4px;
}

.goodslist .item.scroller {
    width: calc(40% - 12px);
    border-radius: 0;
}

.goodslist .scroller img {
    min-height: 237.6px;
}

.goodslist .title .more {
    border: 1px solid #eee;
}

.goodslist .hasimg .item:nth-child(5n-1) {
    margin-right: 0;
}

.goodslist .noimg .item:nth-child(5n) {
    margin-right: 0;
}
</style>

