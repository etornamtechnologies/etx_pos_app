import { addStockUnitToProduct, removeStockUnitFromProduct } from '../api/inventory'

export function AddStockUnitToProduct( product_data ) {
    return new Promise((resolve, reject) => {
        addStockUnitToProduct(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function RemoveStockUnitFromProduct( product_data ) {
    return new Promise((resolve, reject) => {
        removeStockUnitFromProduct(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}