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
                            <el-breadcrumb-item>店铺</el-breadcrumb-item>
                            <el-breadcrumb-item>店铺位置</el-breadcrumb-item>
                        </el-breadcrumb>
                    </div>
                </div>
                <div class="round-edge pd10 bgf mt20">
                    <h3 class="pd10 mb20">坐标设置</h3>
                    <div class="ml20 mr20 mb20">
                        <baidu-map class="map" v-if="baidukey" :ak="baidukey.browser" v="3.0" type="API" :center="position"
                            :zoom="15" :scroll-wheel-zoom="true" @ready="handler">
                            <bm-city-list anchor="BMAP_ANCHOR_TOP_LEFT"></bm-city-list>
                            <bm-navigation anchor="BMAP_ANCHOR_TOP_RIGHT"></bm-navigation>
                            <bm-geolocation anchor="BMAP_ANCHOR_BOTTOM_RIGHT" :showAddressBar="true"
                                :autoLocation="true"></bm-geolocation>
                            <bm-marker :position="position" :dragging="true" @dragend="dragend">
                                <bm-label :content="visitor.store_name" :labelStyle="{ color: 'red' }"
                                    :offset="{ height: -20 }" />
                            </bm-marker>
                        </baidu-map>
                    </div>
                    <div class="mt10 ml20 mb10 f-gray f-13">注：如地图不显示，请联系管理员检查是否正确配置了百度地图KEY</div>
                </div>
            </el-col>
        </el-row>
    </div>
    <myfoot></myfoot>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ElMessage, ElNotification } from 'element-plus'

// https://map.heifahaizei.com/doc/index.html
// tips：地图组件赋值不要在onMounted生命周期
import { BaiduMap, BmMarker, BmGeolocation, BmNavigation, BmCityList, BmLabel } from 'vue-baidu-map-3x'
import { storeRead, storeUpdate } from '@/api/store.js'
import { siteRead } from '@/api/site.js'

import myhead from '@/pages/layout/header/seller.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/seller.vue'

const loading = ref(true)
const visitor = ref({})
const position = ref({ lng: 116.404, lat: 39.915 })
const baidukey = ref(null)

onMounted(() => {
    visitor.value = JSON.parse(localStorage.getItem('visitor')) || {}

    siteRead(null, (data) => {
        baidukey.value = data.baidukey
    }, loading)
})

const handler = ({ BMap, map }) => {
    storeRead({ store_id: visitor.value.store_id }, (data) => {
        position.value = { lat: parseFloat(data.latitude), lng: parseFloat(data.longitude) }
    })
}

const dragend = (value) => {
    let point = value.point
    storeUpdate({ latitude: point.lat, longitude: point.lng }, (data) => {
        ElMessage.success('店铺坐标设置成功！')
    })
}

</script>
<style scoped>
.map {
    border: 1px #ddd solid;
    width: 900px;
    height: 400px;
}
</style>