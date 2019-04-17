import request from '../utils/request'
import { makeQueryString } from '../utils/helpers'

export function getSaleReportByTransaction(_data) {
    const data = _data || null;
    return request({
        url: `/api/reports/sale/transaction`,
        method: 'post',
        data
    })
}


export function getSaleReportByProduct(_data) {
    const data = _data || null;
    return request({
        url: `/api/reports/sale/product`,
        method: 'post',
        data
    });
}


export function getPurchaseReportByTransaction(_data) {
    const data = _data || null;
    return request({
        url: `/api/reports/purchase/transaction`,
        method: 'post',
        data
    })
}


export function getPurchaseReportByProduct(_data) {
    const data = _data || null;
    return request({
        url: `/api/reports/purchase/product`,
        method: 'post',
        data
    });
}


export function getStockAdjustmentReportByTransaction(_data) {
    const data = _data || null;
    let qStr = makeQueryString(data);
    return request({
        url: `/api/reports/stock-adjustment/transaction?${qStr}`,
        method: 'get',
        data
    })
}


export function getStockAdjustmentReportByProduct(_data) {
    const data = _data || null;
    let qStr = makeQueryString(data);
    return request({
        url: `/api/reports/stock-adjustment/product?${qStr}`,
        method: 'get',
        data,
    });
}