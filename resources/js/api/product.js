import request from '../utils/request'
import { makeQueryString } from '../utils/helpers'


export function getProduct(_data) {
    let url = '/api/products';
    let qStr = makeQueryString(_data)
    if(qStr) {
        url = url+`?${qStr}`;
    }
    return request({
        url: url,
        method: 'get',
    })
}

export function getProductDetail(productId) {
    return request({
        url: `/api/products/${productId}`,
        method: 'get',
    })
}



export function createProduct(product_data) {
    const data = product_data;
    return request({
        url: '/api/products',
        method: 'post',
        data
    })
}

export function createProductEntries(product_data) {
    const data = product_data;
    return request({
        url: '/api/products/entries',
        method: 'post',
        data
    })
}

export function updateProduct(product_data) {
    const data = product_data;
    return request({
        url: `/api/products/${data.id}`,
        method: 'put',
        data
    })
}

export function deleteProduct(product_data) {
    const data = product_data;
    return request({
        url: `/api/products/${data.id}`,
        method: 'delete',
    })
}