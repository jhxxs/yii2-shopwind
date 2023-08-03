<template>
    <myhead></myhead>
    <div class="main w pt10">
        <el-row :gutter="12">
            <el-col :span="4">
                <menus></menus>
            </el-col>
            <el-col :span="20">
                <div class="round-edge pd10 bgf">
                    <div class="pd10">
                        <el-breadcrumb separator="/">
                            <el-breadcrumb-item>账户</el-breadcrumb-item>
                            <el-breadcrumb-item>资料修改</el-breadcrumb-item>
                        </el-breadcrumb>
                    </div>
                </div>
                <div class="round-edge pd10 bgf mt20">
                    <h3 class="pd10 mb20">基本信息</h3>
                    <el-form :inline="true" class="pd10">
                        <el-form-item label="头像" :label-width="100">
                            <el-upload action="#" :show-file-list="false" :auto-upload="false" :on-change="fileUpload">
                                <el-avatar v-if="form.portrait" :src="form.portrait + '?t=' + random" :size="120" />
                                <el-icon v-else class="image" :size="20">
                                    <plus />
                                </el-icon>
                            </el-upload>
                        </el-form-item>
                        <el-form-item label="用户ID" :label-width="100">
                            <el-input :value="visitor.username" readonly disabled />
                        </el-form-item>
                        <el-form-item label="昵称" :label-width="100">
                            <el-input v-model="form.nickname" clearable />
                        </el-form-item>
                        <el-form-item label="姓名" :label-width="100">
                            <el-input v-model="form.real_name" clearable />
                        </el-form-item>
                        <el-form-item label="性别" :label-width="100">
                            <el-select v-model="form.gender">
                                <el-option label="男" :value="1" />
                                <el-option label="女" :value="2" />
                                <el-option label="保密" :value="0" />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="生日" :label-width="100">
                            <el-date-picker v-model="form.birthday" type="date" value-format="YYYY-MM-DD" />
                        </el-form-item>
                        <el-form-item label="QQ" :label-width="100">
                            <el-input v-model="form.im_qq" clearable />
                        </el-form-item>
                        <el-form-item label="邮箱" :label-width="100">
                            <el-input v-model="form.email" clearable />
                        </el-form-item>
                        <el-form-item label=" " :label-width="100">
                            <el-button type="primary" @click="submit" :loading="loading">提交</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </el-col>
        </el-row>
    </div>
    <myfoot></myfoot>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ElMessage, ElNotification } from 'element-plus'
import { userRead, userUpdate } from '@/api/user.js'
import { uploadFile } from '@/api/upload.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const loading = ref(false)
const form = reactive({ gender: 0 })
const visitor = ref({ username: '' })
const random = ref('')

onMounted(() => {
    userRead(null, (data) => {
        ['nickname', 'real_name', 'im_qq', 'email', 'birthday', 'gender', 'portrait'].forEach((item) => {
            form[item] = item == 'gender' ? parseInt(data[item]) : data[item]
        })
        visitor.value = data
    })
})

const submit = () => {
    userUpdate(form, (data) => {
        ElNotification({
            title: '提示',
            message: '用户资料修改成功！',
            type: 'success',
            position: 'bottom-left'
        })
    }, loading)
}
const fileUpload = (file) => {
    uploadFile(file.raw, { filename: 'portrait', folder: 'profile/' + visitor.value.userid + '/' }, (data) => {
        form.portrait = data.fileUrl
        userUpdate({ portrait: form.portrait }, () => {
            random.value = Math.random()
            ElMessage.success('头像已修改！')
        })
    })
}

</script>
<style scoped>
.el-form .el-form-item {
    margin-right: 40%;
}

.el-form .el-input,
.el-form .el-select {
    width: 220px;
}

.el-form .image,
.el-form img {
    border: 1px #ccc dotted;
    border-radius: 4px;
}

.el-form image {
    padding: 50px;
}

.el-form img {
    border-radius: 4px;
}
</style>