import * as adminApi from '../api/admin'

export function GetUser( _data ) {
    return new Promise((resolve, reject) => {
        adminApi.apiGetUser(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function AssignRoleToUser( _data ) {
    return new Promise((resolve, reject) => {
        adminApi.apiAssignRole(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function DeleteUser( _data ) {
    return new Promise((resolve, reject) => {
        adminApi.apiDeleteUser(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function ShowUser( _data ) {
    return new Promise((resolve, reject) => {
        adminApi.apiShowUser(_data)
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
        adminApi.apiCreateBackup(_data)
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
        adminApi.apiRestoreBackup(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function ResetUserPassword( _data ) {
    return new Promise((resolve, reject) => {
        adminApi.apiResetUserPassword(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function CreateUser( _data ) {
    return new Promise((resolve, reject) => {
        adminApi.apiCreateUser(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function UpdateUser( _data ) {
    return new Promise((resolve, reject) => {
        adminApi.apiUpdateUser(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}