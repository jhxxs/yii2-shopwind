<template>
    <myhead></myhead>
    <div class="main w pt10">
        <el-row :gutter="12">
            <el-col :span="4">
                <menus></menus>
            </el-col>
            <el-col :span="20">
                <div class="wraper" v-loading="loading">
                    <div class="round-edge pd10 bgf">
                        <div class="pd10">
                            <el-breadcrumb separator="/">
                                <el-breadcrumb-item>资产</el-breadcrumb-item>
                                <el-breadcrumb-item>钱包账户</el-breadcrumb-item>
                            </el-breadcrumb>
                        </div>
                    </div>
                    <div class="round-edge bgf pd10 mt20">
                        <div class="uni-flex uni-row flex-middle pd5">
                            <p>资金账户（{{ deposit.account }}）</p>
                            <p class="flex-middle ml20">
                                <span class="f-gray mr10">余额支付</span>
                                <el-switch v-model="deposit.pay_status" @change="change" :active-value=1
                                    :inactive-value=0 />
                            </p>
                        </div>
                        <div class="mt10 pd5">
                            <span class="f-25 f-c55">{{ currency(deposit.money) }}</span>
                            <router-link to="/deposit/trade/record" class="rlink">
                                <el-icon :size="20">
                                    <ArrowRight />
                                </el-icon>
                            </router-link>
                            <p class="f-gray f-12 ml5 mt10">不可用余额：{{ currency(deposit.frozen) }}</p>
                        </div>
                        <el-divider></el-divider>
                        <div class="uni-flex uni-row flex-middle pd5 mb10">
                            <el-button class="large mr10" @click="redirect('/deposit/trade/recharge')" type="primary">充值
                            </el-button>
                            <el-button class="large" @click="redirect('/deposit/trade/drawal')" type="info">提现</el-button>
                        </div>
                    </div>

                    <div class="pd10 mt5 f-gray bold">最近交易记录</div>
                    <div class="round-edge pd10 bgf mt5" v-loading="loading">
                        <el-table :data="gallery" :border="false" :stripe="false">
                            <el-table-column type="selection" />
                            <el-table-column prop="add_time" label="创建日期" width="100" sortable />
                            <el-table-column width="300" label="交易标题">
                                <template #default="scope">
                                    <p>{{ scope.row.title }}</p>
                                    <p v-if="scope.row.buyer_remark" class="f-gray f-12">{{ scope.row.buyer_remark }}
                                    </p>
                                </template>
                            </el-table-column>
                            <el-table-column prop="amount" label="金额（元）" width="140">
                                <template #default="scope">
                                    <p :class="['f-price', scope.row.flow == 'income' ? 'f-green' : 'f-c60']">
                                        <span v-if="scope.row.flow == 'income'">+</span>
                                        <span v-else>-</span>
                                        <strong>{{ currency(scope.row[scope.column.property], 2, '') }}</strong>
                                    </p>
                                </template>
                            </el-table-column>
                            <el-table-column prop="status" label="订单状态" width="150" sortable>
                                <template #default="scope">
                                    <p class="bold">
                                        <span v-if="scope.row.status == 'CLOSED'" class="f-gray">
                                            {{ translator(scope.row.status, 'trade') }}
                                        </span>
                                        <span v-else-if="scope.row.status == 'SUCCESS'" class="f-blue">
                                            {{ translator(scope.row.status, 'trade') }}
                                        </span>
                                        <span v-else>{{ translator(scope.row.status, 'trade') }}</span>
                                    </p>
                                </template>
                            </el-table-column>
                            <el-table-column prop="tradeNo" label="交易号" width="130" sortable />
                            <el-table-column prop="bizOrderId" label="商户订单号" width="130" sortable />
                            <el-table-column prop="payment_name" label="支付方式" width="150" sortable />
                            <el-table-column fixed="right" label="操作" width="100" align="center">
                                <template #default="scope">
                                    <router-link class="rlink f-blue mb5"
                                        :to="'/deposit/trade/detail/' + scope.row.tradeNo">
                                        查看</router-link>
                                </template>
                            </el-table-column>
                        </el-table>
                        <div class="pd10 center">
                            <router-link class="rlink f-blue mb5" to="/deposit/trade/list">查看更多</router-link>
                        </div>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
    <myfoot></myfoot>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { depositRead, depositUpdate, depositTradeList } from '@/api/deposit.js'
import { currency, translator, redirect } from '@/common/util.js'

import myhead from '@/pages/layout/header/my.vue'
import myfoot from '@/pages/layout/footer/user.vue'
import menus from '@/pages/layout/menus/my.vue'

const loading = ref(false)
const gallery = ref([])
const deposit = ref({ pay_status: 1 })

onMounted(() => {
    depositRead(null, (data) => {
        deposit.value = data
    })
    getList({ page_size: 10 })
})
const change = (value) => {
    depositUpdate({ pay_status: value }, (data) => {
        ElMessage.success(value == 1 ? '余额支付已开启' : '余额支付已关闭')
    })
}

function getList(params) {
    depositTradeList(params, (data) => {
        gallery.value = data.list
    }, loading)
}
</script>

<style scoped>
.el-table,
.el-form-item {
    font-size: 13px;
}

.el-button.large {
    padding-left: 30px;
    padding-right: 30px;
}

.wraper .el-row {
    line-height: 30px;
}
</style>
