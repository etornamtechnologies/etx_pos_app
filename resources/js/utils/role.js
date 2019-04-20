import store from '../store'


/**
 * @param {Array} value
 * @returns {Boolean}
 * @example see @/views/permission/directive.vue
 */

export function HasAnyRole(roleList) {
    if (roleList && roleList instanceof Array && roleList.length > 0) {
        const roles = store.getters.user_roles || []
        const hasRole = roles.some(role => {
            return roleList.includes(role)
        })
        if (!hasRole) {
            return false
        }
        return true
    } else {
        return false
    }
}
