import request from '../utils/request'
import { makeQueryString } from '../utils/helpers'

export function getSale(sale_data) {
    const data = sale_data || {};
    let queryString = makeQueryString(data)
    let url = '/api/sales'
    if(queryString) {
        url = `${url}?${queryString}`
    }
    return request({
        url,
        method: 'get',
    })
}


export function showSale(sale_data) {
    const data = sale_data || {};
    let queryString = makeQueryString(data)
    let url = `/api/sales/${data.id}`
    if(queryString) {
        url = `${url}?${queryString}`
    }
    return request({
        url,
        method: 'get',
    })
}

export function createSale(sale_data) {
    const data = sale_data || {};
    return request({
        url: '/api/sales',
        method: 'post',
        data
    })
}




export function cancelSale(sale_data) {
    const data = sale_data || {};
    return request({
        url: `/api/sales/${data.id}/cancel`,
        method: 'get',
    })
}


export function getPurchase(purchase_data) {
    const data = purchase_data || {};
    let queryString = makeQueryString(data)
    let url = '/api/sales'
    if(queryString) {
        url = `${url}?${queryString}`
    }
    return request({
        url,
        method: 'get',
    })
}


export function createPurchase(purchase_data) {
    const data = purchase_data || {};
    return request({
        url: '/api/purchases',
        method: 'post',
        data
    })
}

export function cancelPurchase(purchase_data) {
    const data = purchase_data || {};
    return request({
        url: `/api/purchases/${data.id}/cancel`,
        method: 'get',
    })
}



export function getStockAdjustment(reason_data) {
    const data = reason_data || null;
    return request({
        url: '/api/stock-adjustments',
        method: 'get',
        data
    })
}

