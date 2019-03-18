import request from '../utils/request'


export function createShopSetup(_data) {
    const data = _data || null;
    return request({
        url: '/api/settings/shop-setup',
        method: 'post',
        data
    })
}

export function updateShopSetup(_data) {
    const data = _data || null;
    return request({
        url: '/api/settings/shop-setup',
        method: 'put',
        data
    })
}

export function getShopSetup(_data) {
    const data = _data || null;
    return request({
        url: '/api/settings/shop-setup',
        method: 'get',
    })
}

export function getBackup(_data) {
    const data = _data || null;
    return request({
        url: '/api/settings/backup',
        method: 'get',
    })
}

export function createBackup(_data) {
    const data = _data || null;
    return request({
        url: '/api/settings/backup/create',
        method: 'get',
    })
}

export function restoreBackup(_data) {
    const data = _data || null;
    return request({
        url: '/api/settings/restore',
        method: 'get',
    })
}
