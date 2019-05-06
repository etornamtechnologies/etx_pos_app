import { createProduct, createProductCsv, updateProduct, deleteProduct, getProduct, createProductEntries, getProductDetail} from '../api/product'

export function CreateProduct( product_data ) {
    return new Promise((resolve, reject) => {
        createProduct(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}


export function CreateProductCsv( product_data ) {
    return new Promise((resolve, reject) => {
        createProductCsv(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}


export function CreateProductEntries( product_data ) {
    return new Promise((resolve, reject) => {
        createProductEntries(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function UpdateProduct( product_data ) {
    return new Promise((resolve, reject) => {
        updateProduct(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function DeleteProduct( product_data ) {
    return new Promise((resolve, reject) => {
        deleteProduct(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetProduct( product_data ) {
    return new Promise((resolve, reject) => {
        getProduct(product_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetProductDetail( productId ) {
    return new Promise((resolve, reject) => {
        getProductDetail(productId)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}