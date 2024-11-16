<template>
    <el-dialog v-model="dialogVisible" :title="title" :width="600" :center="true" :draggable="true" :destroy-on-close="true"
        :close-on-click-modal="false" :before-close="close">
        <div v-if="gallery.length > 0" class="gallery" id="printMe">
            <div v-for="(order) in gallery" class="item mb20">
                <div class="hd">销售清单</div>
                <table>
                    <tr>
                        <td class="noborder" colspan="2"><span>店铺：{{ order.seller_name }}</span></td>
                        <td class="noborder" colspan="2" style="text-align: right;">
                            <span>制单时间：{{ getMoment().format('YYYY-MM-DD HH:mm:ss') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th><span>订单编号</span></th>
                        <td><span>{{ order.order_sn }}</span></td>
                        <th><span>订单状态</span></th>
                        <td><span>{{ translator(order.status) }}</span></td>
                    </tr>
                    <tr>
                        <th><span>订单金额</span></th>
                        <td><span>{{ currency(order.order_amount) }}</span></td>
                        <th><span>配送费用</span></th>
                        <td><span>{{ currency(order.freight) }}</span></td>
                    </tr>
                    <tr>
                        <th><span>支付方式</span></th>
                        <td><span>{{ order.payment_name }}</span></td>
                        <th><span>配送方式</span></th>
                        <td><span>{{ order.deliveryName }}</span></td>
                    </tr>
                    <tr>
                        <th><span>下单时间</span></th>
                        <td><span>{{ order.add_time }}</span></td>
                        <th><span>付款时间</span></th>
                        <td><span>{{ order.pay_time }}</span></td>
                    </tr>
                    <tr>
                        <th><span>发货时间</span></th>
                        <td><span>{{ order.ship_time }}</span></td>
                        <th><span>完成时间</span></th>
                        <td><span>{{ order.finished_time }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="4" align="left"><span>收货人信息</span></td>
                    </tr>
                    <tr>
                        <th><span>收<i style="margin:0 7px">货</i>人</span></th>
                        <td><span>{{ order.consignee.name }}</span></td>
                        <th><span>收货地址</span></th>
                        <td>
                            <span>
                                {{ order.consignee.province || '' }}{{ order.consignee.city || ''}}{{ order.consignee.district || '' }}{{ order.consignee.address }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th><span>联系电话</span></th>
                        <td><span>{{ order.consignee.phone_mob }}</span>
                        </td>
                        <th><span>下单附言</span></th>
                        <td><span>{{ order.postscript }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="4" align="left"><span>商品信息</span></td>
                    </tr>
                    <tr v-for="(goods, index) in order.items">
                        <td colspan="3" align="left">
                            <span> {{ index + 1 }}) {{ goods.goods_name }}
                                <label v-if="goods.specification">({{
                                    goods.specification }})</label>
                            </span>
                        </td>
                        <td><span>{{ currency(goods.price) }} x {{ goods.quantity }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
        <template #footer>
            <el-button @click="close">关闭</el-button>
            <el-button type="primary" v-print="printObj">开始打印</el-button>
        </template>
    </el-dialog>
</template>
<script setup>
import { ref, reactive, watch } from 'vue'
import { currency, translator } from '@/common/util.js'
import { getMoment } from '@/common/moment.js'

const props = defineProps({
    title: { type: String, default: '' },
    visible: { type: Boolean, default: false },
    data: {
        type: [Object, Array],
        default: () => {
            return []
        }
    }
})

const dialogVisible = ref(false)
const gallery = ref(props.data)

watch(() => props.visible, (value) => {
    dialogVisible.value = value
})

watch(() => props.data, (value) => {
    gallery.value = value
})

const printObj = {
    id: "printMe",
    popTitle: "&nbsp",
    openCallback(vue) {
        vue.printLoading = false
        close()
    },
    closeCallback(vue) {
        close()
    }
};

const emit = defineEmits(['close'])
const close = () => {
    emit('close', null)
}

</script>
<style scoped>
@media all {
    .gallery {
        width: 100%;
        height: 500px;
        overflow-y: scroll;
        color: #000;
    }

    .gallery .item {
        page-break-after: always;
    }

    .gallery .item .hd {
        text-align: center;
        line-height: 38px;
        height: 38px;
    }

    .gallery table {
        border-spacing: 0px;
        line-height: 18px;
        border-collapse: separate;
        border: 0;
        width: 90%;
        margin: 0 auto;
    }

    .gallery table th,
    .gallery table td {
        background: #fff;
        width: 15%;
        font-weight: normal;
        padding: 10px 0;
        font-size: 10px;
        border: 1px #000000 solid;
    }

    .gallery table td {
        width: 35%;
    }

    .gallery table tr td span,
    .gallery table tr th span {
        padding: 0 5px;
        display: block;
    }

    .gallery table tr th span i {
        font-style: normal;
    }

    .gallery table td.noborder,
    .gallery table th.noborder {
        border: 0;
        width: 50%;
    }
}

@media print {
    .gallery {
        height: auto;
        overflow-y: visible;
    }
}
</style>