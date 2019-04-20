import { GetUserInfo } from './user'

export function makeQueryString(_data) {
    let data = _data || {};
    let dataList = Object.keys(data) || [];
    let strList = dataList.map(key=> {
        return `${key}=${data[key]}`;
    }) || []
    return strList.join('&');
}

export function formatDate(date_str) {
    let options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    let today  = new Date();
    return today.toLocaleDateString("en-US", options)
}

export function hasAnyRole(roleList, next) {
    GetUserInfo({})
        .then(result=> {
            let user = result.user || {};
            let userRoles = user.roles || [];
            let roles = userRoles.map(r=> {
                return r.label;
            }) || [];
            if (roleList && roleList instanceof Array && roleList.length > 0) {
                console.log('roles', roles)
                let hasRole = roles.some(role => {
                    return roleList.includes(role)
                })
                console.log('role bool',hasRole);
                next(hasRole);
            } else {
                next(false)
            }
        })
}