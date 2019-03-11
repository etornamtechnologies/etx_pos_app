import { createCategory, updateCategory, deleteCategory, getCategory} from '../api/category'

export function CreateCategory( category_data ) {
    return new Promise((resolve, reject) => {
        createCategory(category_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function UpdateCategory( category_data ) {
    return new Promise((resolve, reject) => {
        updateCategory(category_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function DeleteCategory( category_data ) {
    return new Promise((resolve, reject) => {
        deleteCategory(category_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetCategory( category_data ) {
    return new Promise((resolve, reject) => {
        getCategory(category_data)
        .then(response => {
            const data = response || {}
            console.log('dd', data);
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}