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
        console.log('check', hasRole)
        if (!hasRole) {
            return false
        }
        return true
    } else {
        return false
    }
}



export default function checkPermission(value) {
    if (value && value instanceof Array && value.length > 0) {
      const roles = store.getters && store.getters.roles
      const permissionRoles = value
  
      const hasPermission = roles.some(role => {
        return permissionRoles.includes(role)
      })
  
      if (!hasPermission) {
        return false
      }
      return true
    } else {
      console.error(`need roles! Like v-permission="['admin','editor']"`)
      return false
    }
  }