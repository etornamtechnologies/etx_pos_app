import { updateBatch, deleteBatch } from '../api/batch'

export function UpdateBatch( _data ) {
    return new Promise((resolve, reject) => {
        updateBatch(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function DeleteBatch( _data ) {
    return new Promise((resolve, reject) => {
        deleteBatch(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}
