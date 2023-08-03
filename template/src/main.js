/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 * 
 * @Id main.js 2021.8.8 $
 * @author mosir
 */

import { createApp } from 'vue'
//import { createMetaManager} from 'vue-meta'
import App from './App.vue'
import router from './router'
import ElementPlus from 'element-plus'
import * as icons from '@element-plus/icons-vue'
import { isMobile, siteUrl } from './common/util.js'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'

const app = createApp(App)
//app.use(createMetaManager(false, { meta: { tag: 'meta', nameless: true } }))
Object.keys(icons).forEach(key => {
    app.component(key, icons[key])
})
app.use(router)
app.use(ElementPlus)

/**
 * 终端判断
 */
if (isMobile()) {
    window.location.href = siteUrl() + '/h5'
}

/**
 * 路由控制
 */
router.beforeEach((to, from) => {
    NProgress.start()

    // 设置标题
    if (to.meta.title) {
        document.title = to.meta.title
    }

    // 当前访客信息
    const visitor = JSON.parse(localStorage.getItem('visitor')) || {}

    // 需要登录
    if (to.meta.requiresAuth) {

        // 卖家登录或买家登录
        let LOGIN_PAGE = (to.meta.requiresSeller ? '/seller/login' : '/user/login') + '?redirect=' + to.fullPath

        // 进入登录页面
        if ((to.meta.requiresSeller && !visitor.store_id) || !visitor.userid) {
            router.replace(LOGIN_PAGE)
        }
    }

    // 已经登录
    if (visitor.userid && (to.path).indexOf('/login') > -1) {
        if ((to.path).indexOf('/seller') > -1) {
            if (visitor.store_id) {
                router.replace('/seller/index')
            }
        } else {
            router.replace('/my/index')
        }
    }
})
router.afterEach(() => {
    NProgress.done()
})
app.mount('#app')