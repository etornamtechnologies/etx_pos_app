import request from '../utils/request'
import { makeQueryString } from '../utils/helpers'

export function getProductsWithLowQuanity(data) {
    return request({
        url: `/api/alerts/restock-list`,
        method: 'get',
        data
    })
}

export function getProductExpiryAlertList(data) {
    return request({
        url: `/api/alerts/expiry-list`,
        method: 'get',
        data
    })
}