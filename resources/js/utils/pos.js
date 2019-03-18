import * as posApi from '../api/pos'


export function GetSale( _data ) {
    return new Promise((resolve, reject) => {
        posApi.getSale(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function ShowSale( _data ) {
    return new Promise((resolve, reject) => {
        posApi.showSale(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function CreateSale( _data ) {
    return new Promise((resolve, reject) => {
        posApi.createSale(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function CancelSale( _data ) {
    return new Promise((resolve, reject) => {
        posApi.cancelSale(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}



export function GetPurchase( _data ) {
    return new Promise((resolve, reject) => {
        posApi.getPurchase(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function CreatePurchase( _data ) {
    return new Promise((resolve, reject) => {
        posApi.createPurchase(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function CancelPurchase( _data ) {
    return new Promise((resolve, reject) => {
        posApi.cancelPurchase(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}