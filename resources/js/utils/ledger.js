import * as ledgerApi from '../api/ledger'


export function GetSaleWithDebt( _data ) {
    return new Promise((resolve, reject) => {
        ledgerApi.getSalesWithDebt(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetPurchasesWithCredit( _data ) {
    return new Promise((resolve, reject) => {
        ledgerApi.getPurchasesWithCredit(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetAllBatches( _data ) {
    return new Promise((resolve, reject) => {
        ledgerApi.getAllBatches(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}