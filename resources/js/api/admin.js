import request from '../utils/request'


export function apiGetUser(_data) {
    const data = _data || {};
    return request({
        url: '/api/admin/users',
        method: 'get',
    })
}


export function apiAssignRole(_data) {
    const data = _data || {};
    return request({
        url: `/api/admin/users/${data.id}/assign-role`,
        method: 'put',
        data
    })
}

export function apiDeleteUser(_data) {
    const data = _data || {};
    return request({
        url: `/api/admin/users/${data.id}`,
        method: 'delete',
        data
    })
}

export function apiShowUser(_data) {
    const data = _data || {};
    return request({
        url: `/api/admin/users/${data.id}`,
        method: 'get',
        data
    })
}

export function apiCreateBackup(_data) {
    const data = _data || {};
    return request({
        url: `/api/admin/create-backup`,
        method: 'get',
    })
}

export function apiRestoreBackup(_data) {
    const data = _data || {};
    return request({
        url: `/api/admin/restore-backup/${data.id}`,
        method: 'get',
        data
    })
}

export function apiResetUserPassword(_data) {
    const data = _data || {};
    return request({
        url: `/api/admin/users/${data.id}/reset-password`,
        method: 'post',
        data
    })
}
