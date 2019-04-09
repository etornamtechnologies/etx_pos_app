import store from '../store'

export function checkRole(value) {
    console.log('check', value)
    if (value && value instanceof Array && value.length > 0) {
        const roles = store.getters.user_roles || []
        console.log('from store',roles)
        const permissionRoles = value
        const hasRole = roles.some(role => {
            return roles.includes(role)
        })
        if (!hasRole) {
            return false
        }
        return true
    } else {
        return false
    }
}