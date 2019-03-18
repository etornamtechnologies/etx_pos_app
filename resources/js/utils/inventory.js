import * as inventoryAPi from '../api/inventory'

export function AddStockUnitToProduct( product_data ) {
    return new Promise((resolve, reject) => {
        inventoryAPi.addStockUnitToProduct(product_data)
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
        inventoryAPi.removeStockUnitFromProduct(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}


export function GetCustomer( _data ) {
    return new Promise((resolve, reject) => {
        inventoryAPi.getCustomer(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function CreateCustomer( _data ) {
    return new Promise((resolve, reject) => {
        inventoryAPi.postCustomer(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function UpdateCustomer( _data ) {
    return new Promise((resolve, reject) => {
        inventoryAPi.putCustomer(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function DeleteCustomer( _data ) {
    return new Promise((resolve, reject) => {
        inventoryAPi.deleteCustomer(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}



export function GetSupplier( _data ) {
    return new Promise((resolve, reject) => {
        inventoryAPi.getSupplier(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function CreateSupplier( _data ) {
    return new Promise((resolve, reject) => {
        inventoryAPi.postSupplier(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function UpdateSupplier( _data ) {
    return new Promise((resolve, reject) => {
        inventoryAPi.putSupplier(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}


export function DeleteSupplier( _data ) {
    return new Promise((resolve, reject) => {
        inventoryAPi.deleteSupplier(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}