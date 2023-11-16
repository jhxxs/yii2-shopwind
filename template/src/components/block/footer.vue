<template>
    <div class="footer" :style="{ '--bgcolor': options.bgcolor, '--space': options.space + 'px' }">
        <div :class="['wraper', options.showfull == 1 ? 'ml10 mr10' : 'w']">
            <div class="uni-flex uni-row flex-middle flex-center pd10 f-c55">
                <p v-for="(item, index) in options.navs">
                    <span @click="redirect(item.link)" class="pointer rlink">{{ item.title }}</span>
                    <i class="divider inline-block ml10 mr10"></i>
                </p>
                <p><span @click="clear" class="pointer rlink">清除缓存</span></p>
            </div>
            <div class="info f-gray center f-13 pb10">
                <span v-if="options.copyright">{{ options.copyright }}</span>
                <span v-else>Copyright © 2015-{{ new Date().getFullYear() }} SHOPWIND.NET 版权所有</span>
                <a v-if="site.icp" class="ml10 rlink f-gray" href="https://beian.miit.gov.cn" target="_blank">备案号：{{ site.icp }}</a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { ElMessage } from 'element-plus'
import { redirect } from '@/common/util.js'
import { siteRead } from '@/api/site.js'

const props = defineProps({
    options: {
        type: Object,
        default() {
            return {}
        }
    }
})

const loading = ref(false)
const site = reactive({})
onMounted(() => {
    siteRead(null, (data) => {
        site.icp = data.icp
    }, loading)
})
const clear = () => {
    localStorage.clear()
    ElMessage.success('缓存已清除')
}

</script>
<style scoped>
.footer {
    background: var(--bgcolor);
    padding: var(--space) 0;
}

.footer .divider {
    width: 1px;
    height: 10px;
    background-color: #dddddd;
}
</style>

