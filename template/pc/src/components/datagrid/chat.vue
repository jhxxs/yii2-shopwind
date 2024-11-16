<template>

	<el-backtop v-if="route.path.indexOf('/webim/chat') < 0" @click="redirect('/webim/chat' + (message.lastid > 0 ? '/' + message.lastid : ''))" :right="10"
		:bottom="150" :visibility-height="0">
		<div class="relative">
			<el-icon>
				<ChatLineRound />
			</el-icon>
			<span v-if="message.unread > 0" class="absolute unread f-10 f-white center">{{ message.unread }}</span>
		</div>
	</el-backtop>
	
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { redirect } from '@/common/util.js'
import { webimUnread, webimList } from '@/api/webim.js'

const route = useRoute()
const visitor = ref({})
const message = reactive({ unread: 0, lastid: 0 })

onMounted(() => {
	visitor.value = JSON.parse(localStorage.getItem('visitor')) || {}

	if (visitor.value.userid && route.path.indexOf('/webim/chat') < 0) {
		setInterval(() => {
			webimUnread(null, (data) => {
				if (data > message.unread) {
					webimList(null, (res) => {
						for (let i = 0; i < res.length; i++) {
							if (res[i].unreads > 0 && res[i].to) {
								message.lastid = res[i].to.userid
								break
							}
						}
					})
					message.unread = data
				}
			})
		}, 4000)
	}
})

</script>
<style scoped>
.unread {
	background-color: var(--my-color-red);
	padding: 0px 4px;
	border-radius: 20px;
	top: -8px;
	right: -6px;
}
</style>
