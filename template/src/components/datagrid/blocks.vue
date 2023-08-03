<template>
    <div v-for="(block, index) in floors" :key="index">
        <div v-if="!inArray(block.name, exclude)">
            <div v-if="block.name == 'navbar'">
                <blockNavbar :options="block.options"></blockNavbar>
            </div>
            <div v-if="block.name == 'imagead'">
                <blockImagead :options="block.options"></blockImagead>
            </div>
            <div v-if="block.name == 'searchbox'">
                <blockSearchbox :options="block.options"></blockSearchbox>
            </div>
            <div v-if="block.name == 'category'">
                <blockCategory :options="block.options"></blockCategory>
            </div>
            <div v-if="block.name == 'swiper'">
                <blockSwiper :options="block.options"></blockSwiper>
            </div>
            <div v-if="block.name == 'blank'">
                <blockBlank :options="block.options"></blockBlank>
            </div>
            <div v-if="block.name == 'limitbuy'">
                <blockLimitbuy :options="block.options"></blockLimitbuy>
            </div>
            <div v-if="block.name == 'goodslist'">
                <blockGoodslist :options="block.options"></blockGoodslist>
            </div>
            <div v-if="block.name == 'notice'">
                <blockNotice :options="block.options"></blockNotice>
            </div>
            <div v-if="block.name == 'titlebar'">
                <blockTitlebar :options="block.options"></blockTitlebar>
            </div>
            <div v-if="block.name == 'textbox'">
                <blockTextbox :options="block.options"></blockTextbox>
            </div>
            <div v-if="block.name == 'horzline'">
                <blockHorzline :options="block.options"></blockHorzline>
            </div>
            <div v-if="block.name == 'footer'">
                <blockFooter :options="block.options"></blockFooter>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { inArray, redirect } from '@/common/util.js'
import { templateBlock } from '@/api/template.js'

import blockNavbar from '@/components/block/navbar.vue'
import blockImagead from '@/components/block/imagead.vue'
import blockSearchbox from '@/components/block/searchbox.vue'
import blockCategory from '@/components/block/category.vue'
import blockSwiper from '@/components/block/swiper.vue'
import blockLimitbuy from '@/components/block/limitbuy.vue'
import blockGoodslist from '@/components/block/goodslist.vue'
import blockNotice from '@/components/block/notice.vue'
import blockFooter from '@/components/block/footer.vue'
import blockBlank from '@/components/block/blank.vue'
import blockTitlebar from '@/components/block/titlebar.vue'
import blockTextbox from '@/components/block/textbox.vue'
import blockHorzline from '@/components/block/horzline.vue'

const props = defineProps({
    page: {
        type: String,
        default: 'index'
    },
    exclude: {
        type: Array,
        default() {
            return []
        }
    },
    global: {
        type: [Boolean, String],
        default: ''
    },
    header: {
        type: Boolean,
        default: false
    },
    footer: {
        type: Boolean,
        default: false
    }
})

const floors = ref([])
onMounted(() => {
    templateBlock({ client: 'pc', page: props.page, global: props.global, header: props.header, footer: props.footer }, (data) => {
        if (parseInt(data.enabled) == 1) {
            floors.value = data.list
            if (data.title) document.title = data.title
        } else redirect('/message/result', 'warn', '页面不存在')
    })
})

</script>
