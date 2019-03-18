import { createReason,  getReason,createStockAdjustment, getStockAdjustment} from '../api/adjustment'

export function CreateReason( reason_data ) {
    return new Promise((resolve, reject) => {
        createReason(reason_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetReason( reason_data ) {
    return new Promise((resolve, reject) => {
        getReason(reason_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function CreateStockAdjustment( adj_data ) {
    return new Promise((resolve, reject) => {
        createStockAdjustment(adj_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetStockAdjustment( adj_data ) {
    return new Promise((resolve, reject) => {
        getStockAdjustment(adj_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}