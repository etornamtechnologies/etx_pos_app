import request from '../utils/request'
import { makeQueryString } from '../utils/helpers'

export function getSalesWithDebt(data) {
    return request({
        url: `/api/ledgers/sales-with-debt`,
        method: 'get',
        data
    })
}

export function getPurchasesWithCredit(data) {
    return request({
        url: `/api/ledgers/purchases-with-credit`,
        method: 'get',
        data
    })
}

export function getAllBatches(data) {
    return request({
        url: `/api/ledgers/batches`,
        method: 'get',
        data
    })
}