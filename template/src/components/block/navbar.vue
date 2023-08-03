<template>
    <div class="navbar" :style="{ '--bgcolor': options.bgcolor, '--space': options.space + 'px' }" v-loading="loading">
        <div :class="['wraper', (options.showfull == 1 && route.path == '/') ? 'ml10 mr10' : 'w']">
            <div class="content uni-flex uni-row f-12 flex-middle pt10 pb10">
                <div class="uni-flex uni-row login">
                    <label>您好，</label>
                    <router-link v-if="visitor.userid" to="/my/index" class="rlink f-red">
                        {{ visitor.nickname || visitor.username }}
                    </router-link>
                    <router-link v-else to="/user/login" class="rlink f-red">请登录</router-link>
                </div>
                <div class="uni-flex uni-row flex-middle flex-end width-surplus">
                    <p v-if="route.path != '/'">
                        <router-link to="/" class="rlink f-red">回到首页</router-link>
                        <i v-if="options.navs && options.navs.length > 0" class="divider inline-block ml20 mr20"></i>
                    </p>
                    <div class="uni-flex uni-row" v-if="options.navs && options.navs.length > 0">
                        <p v-for="(item, index) in options.navs">
                            <span @click="redirect(item.link)" class="pointer rlink">{{ item.title }}</span>
                            <i v-if="index != options.navs.length - 1" class="divider inline-block ml20 mr20"></i>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { redirect } from '@/common/util.js'

const props = defineProps({
    options: {
        type: Object,
        default() {
            return {}
        }
    }
})

const route = useRoute()
const loading = ref(true)
const visitor = ref({})
onMounted(() => {
    visitor.value = JSON.parse(localStorage.getItem('visitor')) || {}
    loading.value = false
})

</script>
<style scoped>
.navbar {
    background: var(--bgcolor);
    padding: var(--space) 0;
}

.navbar .divider {
    width: 1px;
    height: 10px;
    background-color: #dddddd;
}
</style>

