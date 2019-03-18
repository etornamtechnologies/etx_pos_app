import request from '../utils/request'


export function createReason(reason_data) {
    const data = reason_data || null;
    return request({
        url: '/api/stock-adjustment-reasons',
        method: 'post',
        data
    })
}

export function getReason(reason_data) {
    const data = reason_data || null;
    return request({
        url: '/api/stock-adjustment-reasons',
        method: 'get',
        data
    })
}

export function createStockAdjustment(reason_data) {
    const data = reason_data || null;
    return request({
        url: '/api/stock-adjustments',
        method: 'post',
        data
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

