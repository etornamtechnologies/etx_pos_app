import * as reportApi from '../api/report'

export function GetSaleReportByTransaction( product_data ) {
    return new Promise((resolve, reject) => {
        reportApi.getSaleReportByTransaction(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetSaleReportByProduct( product_data ) {
    return new Promise((resolve, reject) => {
        reportApi.getSaleReportByProduct(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}


export function GetPurchaseReportByTransaction( product_data ) {
    return new Promise((resolve, reject) => {
        reportApi.getPurchaseReportByTransaction(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetPurcahseReportByProduct( product_data ) {
    return new Promise((resolve, reject) => {
        reportApi.getPurchaseReportByProduct(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}


export function GetStockAdjusmentReportByTransaction( product_data ) {
    return new Promise((resolve, reject) => {
        reportApi.geStockAdjustmentReportByTransaction(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}


export function GetStockAdjustmentReportByProduct( product_data ) {
    return new Promise((resolve, reject) => {
        reportApi.getStockAdjustmentReportByProduct(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

