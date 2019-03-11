import request from '../utils/request'


export function addStockUnitToProduct(data) {
    return request({
        url: `/api/inventory/products/${data.product_id}/stock-units/${data.stock_unit_id}/add?metric_scale=${data.metric_scale}`,
        method: 'get',
        data
    })
}

export function removeStockUnitFromProduct(data) {
    return request({
        url: `/api/inventory/products/${data.product_id}/stock-units/${data.stock_unit_id}/remove`,
        method: 'get',
        data
    })
}
