import * as notificationApi from '../api/notification'


export function GetProductWithLowQuantity( _data ) {
    return new Promise((resolve, reject) => {
        notificationApi.getProductsWithLowQuanity(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetProductExpiryAlertList( _data ) {
    return new Promise((resolve, reject) => {
        notificationApi.getProductExpiryAlertList(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}