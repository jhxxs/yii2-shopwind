<template>
    <div class="titlebar"
        :style="{ '--bgcolor': options.bgcolor, '--txtcolor': options.txtcolor || '#FF8000', '--txthide': options.txthide == 1 ? 0 : 1, '--fontsize': options.fontsize + 'px', '--txtbold': options.txtbold == 1 ? 'bold' : 'normal', '--linecolor': options.linecolor||'#cccccc', '--space': options.space + 'px', '--background': options.image ? 'url(' + options.image + ') center center no-repeat' : '' }" v-loading="loading">
        <div :class="['wraper', options.showfull == 1 ? 'ml10 mr10' : 'w']">
            <div :class="['content center pd10', options.theme]">
                <div class="relative inline-block">
                    <i class="lp absolute inline-block"></i>
                    <span class="text inline-block f-14 pl20 pr20">{{ options.label || '标题' }}</span>
                    <i class="rp absolute inline-block"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue'

const props = defineProps({
    options: {
        type: Object,
        default() {
            return {}
        }
    }
})

const loading = ref(false)
onMounted(() => {
    loading.value = false
})
</script>
<style scoped>
.titlebar {
    background: var(--bgcolor);
    padding: var(--space) 0;
}

.titlebar .wraper {
    background: var(--background);
    background-size: contain;
}

.titlebar .content {
    opacity: var(--txthide);
}

.titlebar .content .text {
    color: var(--txtcolor);
    font-size: var(--fontsize);
    font-weight: var(--txtbold);
}

.titlebar .content .lp,
.titlebar .content .rp {
    height: 1px;
    width: 28px;
    overflow: hidden;
    background: var(--linecolor);
    top: 50%;
}

.titlebar .content .lp {
    left: 0;
    margin-left: -28px;
}

.titlebar .content .rp {
    right: 0;
    margin-right: -28px;
}

.titlebar .content.square .text {
    padding: 4px 10px;
    border: 1px #FF8000 solid;
    border-color: var(--linecolor);
}

.titlebar .content.square .lp,
.titlebar .content.square .rp {
    top: 46%;
    height: 3px;
}

.titlebar .content.leftline .lp,
.titlebar .content.leftline .rp {
    display: none;
}

.titlebar .content.leftline {
    text-align: left;
}

.titlebar .content.leftline .text {
    border-left: 4px #FF8000 solid;
    border-left-color: var(--linecolor);
}

.titlebar .content.dotted .lp,
.titlebar .content.dotted .rp {
    background: url('@/assets/images/sprite.png') no-repeat;
    width: 25px;
    height: 20px;
    margin-top: -10px;
}
</style>

