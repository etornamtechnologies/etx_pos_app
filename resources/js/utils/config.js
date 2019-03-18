import * as configApi from '../api/config'

export function CreateShopSetup( _data ) {
    return new Promise((resolve, reject) => {
        configApi.createShopSetup(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function UpdateShopSetup( _data ) {
    return new Promise((resolve, reject) => {
        configApi.updateShopSetup(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetShopSetup( _data ) {
    return new Promise((resolve, reject) => {
        configApi.getShopSetup(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}


export function GetBackup( _data ) {
    return new Promise((resolve, reject) => {
        configApi.getBackup(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function CreateBackup( _data ) {
    return new Promise((resolve, reject) => {
        configApi.createBackup(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function RestoreBackup( _data ) {
    return new Promise((resolve, reject) => {
        configApi.restoreBackup(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}