import request from '../utils/request'
import { makeQueryString } from '../utils/helpers'

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


export function getCustomer(_data) {
    let url = '/api/customers'
    let queryStr = makeQueryString(_data);
    if(queryStr) url=`${url}?${queryStr}`
    return request({
        url,
        method: 'get'
    })
}


export function postCustomer(_data){
    return request({
        url: '/api/customers',
        method: 'post',
        data: _data
    })
}

export function putCustomer(_data){
    return request({
        url: `/api/customers/${_data.id}`,
        method: 'put',
        data: _data
    })
}

export function deleteCustomer(_data){
    return request({
        url: `/api/customers/${_data.id}`,
        method: 'delete',
    })
}



export function getSupplier(_data) {
    let url = '/api/suppliers'
    let queryStr = makeQueryString(_data);
    if(queryStr) url=`${url}?${queryStr}`
    return request({
        url,
        method: 'get'
    })
}

export function postSupplier(_data){
    return request({
        url: `/api/suppliers`,
        method: 'post',
        data: _data
    })
}

export function putSupplier(_data){
    return request({
        url: `/api/suppliers/${_data.id}`,
        method: 'put',
        data: _data
    })
}

export function deleteSupplier(_data){
    return request({
        url: `/api/suppliers/${_data.id}`,
        method: 'delete',
    })
}