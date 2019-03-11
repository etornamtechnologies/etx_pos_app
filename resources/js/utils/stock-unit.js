import { createStockUnit, updateStockUnit, deleteStockUnit, getStockUnit} from '../api/stock-unit'

export function CreateStockUnit( sku_data ) {
    return new Promise((resolve, reject) => {
        createStockUnit(sku_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function UpdateStockUnit( sku_data ) {
    return new Promise((resolve, reject) => {
        updateStockUnit(sku_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function DeleteStockUnit( sku_data ) {
    return new Promise((resolve, reject) => {
        deleteStockUnit(sku_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetStockUnit( sku_data ) {
    return new Promise((resolve, reject) => {
        getStockUnit(sku_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}