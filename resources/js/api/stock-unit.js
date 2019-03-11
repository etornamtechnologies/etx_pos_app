import request from '../utils/request'


export function getStockUnit(sku_data) {
    const data = sku_data;
    return request({
        url: '/api/stock-units',
        method: 'get',
        data
    })
}



export function createStockUnit(sku_data) {
    const data = sku_data;
    return request({
        url: '/api/stock-units',
        method: 'post',
        data
    })
}

export function updateStockUnit(sku_data) {
    const data = sku_data;
    return request({
        url: `/api/stock-units/${data.id}`,
        method: 'put',
        data
    })
}

export function deleteStockUnit(sku_data) {
    const data = sku_data;
    return request({
        url: `/api/stock-units/${data.id}`,
        method: 'delete',
    })
}