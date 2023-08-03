<template>
    <div class="countdown" :style="{ color: color }">
        <span v-if="showDay && body.d > 0" :style="{ borderColor: borderColor, backgroundColor: backgroundColor }"
            class="countdown__number">{{ body.d }}</span>
        <span v-if="showDay && body.d > 0" :style="{ color: splitorColor, }" class="countdown__splitor">天</span>
        <span :style="{ borderColor: borderColor, color: color, backgroundColor: backgroundColor }"
            class="countdown__number">{{ body.h }}</span>
        <span :style="{ color: splitorColor }" class="countdown__splitor">{{ showColon ? ':' : '时' }}</span>
        <span :style="{ borderColor: borderColor, color: color, backgroundColor: backgroundColor }"
            class="countdown__number">{{ body.i }}</span>
        <span :style="{ color: splitorColor }" class="countdown__splitor">{{ showColon ? ':' : '分' }}</span>
        <span :style="{ borderColor: borderColor, backgroundColor: backgroundColor }" class="countdown__number">{{
                body.s
        }}</span>
        <span v-if="!showColon" :style="{ color: splitorColor }" class="countdown__splitor">秒</span>
    </div>
</template>
<script setup>
/**
 * Countdown 倒计时
 * @description 倒计时组件
 * @property {String} backgroundColor 背景色
 * @property {String} color 文字颜色
 * @property {Number} day 天数
 * @property {Number} hour 小时
 * @property {Number} minute 分钟
 * @property {Number} second 秒
 * @property {Number} timestamp 时间戳
 * @property {Boolean} showDay = [true|false] 是否显示天数
 * @property {Boolean} showColon = [true|false] 是否以冒号为分隔符
 * @property {String} splitorColor 分割符号颜色
 * @event {Function} timeup 倒计时时间到触发事件
 * @example <countdown :day="1" :hour="1" :minute="12" :second="40"></countdown>
 */

import { ref, reactive, watch, onMounted } from 'vue'

const props = defineProps({
    showDay: {
        type: Boolean,
        default: true
    },
    showColon: {
        type: Boolean,
        default: true
    },
    backgroundColor: {
        type: String,
        default: ''
    },
    borderColor: {
        type: String,
        default: '#000000'
    },
    color: {
        type: String,
        default: '#ffffff'
    },
    splitorColor: {
        type: String,
        default: '#000000'
    },
    day: {
        type: Number,
        default: 0
    },
    hour: {
        type: Number,
        default: 0
    },
    minute: {
        type: Number,
        default: 0
    },
    second: {
        type: Number,
        default: 0
    },
    timestamp: {
        type: Number,
        default: 0
    }
})

const timer = ref(null)
const body = reactive({ d: '00', h: '00', i: '00', s: '00' })
const countdown = reactive({ syncFlag: false, leftTime: 0, seconds: 0 })

watch(() => props.day, (value) => {
    changeFlag()
})
watch(() => props.hour, (value) => {
    changeFlag()
})
watch(() => props.minute, (value) => {
    changeFlag()
})
watch(() => props.second, (value) => {
    changeFlag()
})
watch(() => props.timestamp, (value) => {
    changeFlag()
})

onMounted(() => {
    startData()
})

function toSeconds(timestamp, day, hours, minutes, seconds) {
    if (timestamp) {
        return timestamp
    }
    return day * 60 * 60 * 24 + hours * 60 * 60 + minutes * 60 + seconds
}
function timeUp() {
    clearInterval(timer.value)
}
function countDown() {
    let seconds = countdown.seconds
    let [day, hour, minute, second] = [0, 0, 0, 0]
    if (seconds > 0) {
        day = Math.floor(seconds / (60 * 60 * 24))
        hour = Math.floor(seconds / (60 * 60)) - (day * 24)
        minute = Math.floor(seconds / 60) - (day * 24 * 60) - (hour * 60)
        second = Math.floor(seconds) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60)
    } else {
        this.timeUp()
    }
    if (day < 10) {
        day = day
    }
    if (hour < 10) {
        hour = hour
    }
    if (minute < 10) {
        minute = '0' + minute
    }
    if (second < 10) {
        second = '0' + second
    }
    body.d = day
    body.h = hour
    body.i = minute
    body.s = second
}
function startData() {
    countdown.seconds = toSeconds(countdown.timestamp, props.day, props.hour, props.minute, props.second)
    if (countdown.seconds <= 0) {
        return
    }
    countDown()
    timer.value = setInterval(() => {
        countdown.seconds--
        if (countdown.seconds < 0) {
            timeUp()
            return
        }
        countDown()
    }, 1000)
}
function changeFlag() {
    if (!countdown.syncFlag) {
        countdown.seconds = toSeconds(countdown.timestamp, props.day, props.hour, props.minute, props.second)
        startData();
        countdown.syncFlag = true;
    }
}

</script>
<style scoped>
.countdown {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}

.countdown__splitor {
    display: flex;
    justify-content: center;
    line-height: 24px;
    padding: 2px;
}

.countdown__number {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 24px;
    line-height: 24px;
    margin: 2px;
    text-align: center;
}
</style>