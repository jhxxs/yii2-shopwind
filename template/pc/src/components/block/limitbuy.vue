<template>
    <div class="limitbuybox" :style="{ '--bgcolor': options.bgcolor, '--space': (options.space || 10) + 'px' }"
        v-loading="loading">
        <div :class="['wraper relative', options.showfull == 1 ? '' : 'w']">
            <div class="content uni-flex uni-row">
                <div class="lp">
                    <img @click="redirect(options.link)" v-if="options.image" :src="options.image"
                        class="w-full block pointer" :height="height" />
                </div>
                <div class="rp width-surplus pl10 bgf">
                    <div class="uni-flex uni-row">

                        <div v-for="(item, index) in gallery" ref="goods" class="bgf mr10 center item pd15">
                            <router-link :to="'/goods/detail/' + item.goods_id" class="pic rlink">
                                <img @load="onload(index)" :src="item.goods_image" class="w-full block" />
                            </router-link>
                            <router-link :to="'/goods/detail/' + item.goods_id" class="desc line-clamp-2 mt10 rlink">
                                {{ item.goods_name }}
                            </router-link>
                            <p class="price mt10 line-clamp-1">
                                <span class="f-red">{{ currency(item.promotion.price) }}</span>
                                <del class="f-gray ml10">{{ currency(item.price) }}</del>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch, getCurrentInstance } from 'vue'
import { limitbuyList } from '@/api/limitbuy.js'
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
const height = ref(0)

onMounted(() => {
    limitbuyList({ items: props.options.source == 'choice' ? props.options.items : '', page_size: 5 }, (data) => {
        gallery.value = data.list || []
    }, loading)
})

const onload = (index) => {
    if (gallery.value.length > 0 && index == 0) {
        setTimeout(() => {
            height.value = proxy.$refs.goods[0].clientHeight
        }, 100);
    }
}

</script>
<style scoped>
.limitbuybox {
    background: var(--bgcolor);
    padding: var(--space) 0;
}

.limitbuybox .lp {
    width: 16.7%;
}

.limitbuybox .rp .item {
    border-radius: 4px;
    width: calc(20% - 40px);
}

.limitbuybox .wraper.w .rp .item:last-child {
    margin-right: 0;
    width: calc(20% - 30px);
}
</style>
