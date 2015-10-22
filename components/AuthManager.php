<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 22.10.2015
 * Time: 10:24
 */

namespace app\admin\components;


use app\admin\models\User;
use yii\rbac\Assignment;
use yii\rbac\Item;
use yii\rbac\ManagerInterface;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\rbac\Rule;

class AuthManager implements ManagerInterface
{

    /**
     * Checks if the user has the specified permission.
     * @param string|integer $userId the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param string $permissionName the name of the permission to be checked against
     * @param array $params name-value pairs that will be passed to the rules associated
     * with the roles and permissions assigned to the user.
     * @return boolean whether the user has the specified permission.
     * @throws \yii\base\InvalidParamException if $permissionName does not refer to an existing permission
     */
    public function checkAccess($userId, $permissionName, $params = [])
    {
        return (bool)User::find()->select('can_admin')->where([User::tableName() . '.id' => $userId])->joinWith('role', false)->scalar();
    }

    /**
     * Creates a new Role object.
     * Note that the newly created role is not added to the RBAC system yet.
     * You must fill in the needed data and call [[add()]] to add it to the system.
     * @param string $name the role name
     * @return Role the new Role object
     */
    public function createRole($name)
    {
        // TODO: Implement createRole() method.
    }

    /**
     * Creates a new Permission object.
     * Note that the newly created permission is not added to the RBAC system yet.
     * You must fill in the needed data and call [[add()]] to add it to the system.
     * @param string $name the permission name
     * @return Permission the new Permission object
     */
    public function createPermission($name)
    {
        // TODO: Implement createPermission() method.
    }

    /**
     * Adds a role, permission or rule to the RBAC system.
     * @param Role|Permission|Rule $object
     * @return boolean whether the role, permission or rule is successfully added to the system
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    public function add($object)
    {
        // TODO: Implement add() method.
    }

    /**
     * Removes a role, permission or rule from the RBAC system.
     * @param Role|Permission|Rule $object
     * @return boolean whether the role, permission or rule is successfully removed
     */
    public function remove($object)
    {
        // TODO: Implement remove() method.
    }

    /**
     * Updates the specified role, permission or rule in the system.
     * @param string $name the old name of the role, permission or rule
     * @param Role|Permission|Rule $object
     * @return boolean whether the update is successful
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    public function update($name, $object)
    {
        // TODO: Implement update() method.
    }

    /**
     * Returns the named role.
     * @param string $name the role name.
     * @return null|Role the role corresponding to the specified name. Null is returned if no such role.
     */
    public function getRole($name)
    {
        // TODO: Implement getRole() method.
    }

    /**
     * Returns all roles in the system.
     * @return Role[] all roles in the system. The array is indexed by the role names.
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * Returns the roles that are assigned to the user via [[assign()]].
     * Note that child roles that are not assigned directly to the user will not be returned.
     * @param string|integer $userId the user ID (see [[\yii\web\User::id]])
     * @return Role[] all roles directly or indirectly assigned to the user. The array is indexed by the role names.
     */
    public function getRolesByUser($userId)
    {
        // TODO: Implement getRolesByUser() method.
    }

    /**
     * Returns the named permission.
     * @param string $name the permission name.
     * @return null|Permission the permission corresponding to the specified name. Null is returned if no such permission.
     */
    public function getPermission($name)
    {
        // TODO: Implement getPermission() method.
    }

    /**
     * Returns all permissions in the system.
     * @return Permission[] all permissions in the system. The array is indexed by the permission names.
     */
    public function getPermissions()
    {
        // TODO: Implement getPermissions() method.
    }

    /**
     * Returns all permissions that the specified role represents.
     * @param string $roleName the role name
     * @return Permission[] all permissions that the role represents. The array is indexed by the permission names.
     */
    public function getPermissionsByRole($roleName)
    {
        // TODO: Implement getPermissionsByRole() method.
    }

    /**
     * Returns all permissions that the user has.
     * @param string|integer $userId the user ID (see [[\yii\web\User::id]])
     * @return Permission[] all permissions that the user has. The array is indexed by the permission names.
     */
    public function getPermissionsByUser($userId)
    {
        // TODO: Implement getPermissionsByUser() method.
    }

    /**
     * Returns the rule of the specified name.
     * @param string $name the rule name
     * @return null|Rule the rule object, or null if the specified name does not correspond to a rule.
     */
    public function getRule($name)
    {
        // TODO: Implement getRule() method.
    }

    /**
     * Returns all rules available in the system.
     * @return Rule[] the rules indexed by the rule names
     */
    public function getRules()
    {
        // TODO: Implement getRules() method.
    }

    /**
     * Adds an item as a child of another item.
     * @param Item $parent
     * @param Item $child
     * @throws \yii\base\Exception if the parent-child relationship already exists or if a loop has been detected.
     */
    public function addChild($parent, $child)
    {
        // TODO: Implement addChild() method.
    }

    /**
     * Removes a child from its parent.
     * Note, the child item is not deleted. Only the parent-child relationship is removed.
     * @param Item $parent
     * @param Item $child
     * @return boolean whether the removal is successful
     */
    public function removeChild($parent, $child)
    {
        // TODO: Implement removeChild() method.
    }

    /**
     * Removed all children form their parent.
     * Note, the children items are not deleted. Only the parent-child relationships are removed.
     * @param Item $parent
     * @return boolean whether the removal is successful
     */
    public function removeChildren($parent)
    {
        // TODO: Implement removeChildren() method.
    }

    /**
     * Returns a value indicating whether the child already exists for the parent.
     * @param Item $parent
     * @param Item $child
     * @return boolean whether `$child` is already a child of `$parent`
     */
    public function hasChild($parent, $child)
    {
        // TODO: Implement hasChild() method.
    }

    /**
     * Returns the child permissions and/or roles.
     * @param string $name the parent name
     * @return Item[] the child permissions and/or roles
     */
    public function getChildren($name)
    {
        // TODO: Implement getChildren() method.
    }

    /**
     * Assigns a role to a user.
     *
     * @param Role $role
     * @param string|integer $userId the user ID (see [[\yii\web\User::id]])
     * @return Assignment the role assignment information.
     * @throws \Exception if the role has already been assigned to the user
     */
    public function assign($role, $userId)
    {
        // TODO: Implement assign() method.
    }

    /**
     * Revokes a role from a user.
     * @param Role $role
     * @param string|integer $userId the user ID (see [[\yii\web\User::id]])
     * @return boolean whether the revoking is successful
     */
    public function revoke($role, $userId)
    {
        // TODO: Implement revoke() method.
    }

    /**
     * Revokes all roles from a user.
     * @param mixed $userId the user ID (see [[\yii\web\User::id]])
     * @return boolean whether the revoking is successful
     */
    public function revokeAll($userId)
    {
        // TODO: Implement revokeAll() method.
    }

    /**
     * Returns the assignment information regarding a role and a user.
     * @param string|integer $userId the user ID (see [[\yii\web\User::id]])
     * @param string $roleName the role name
     * @return null|Assignment the assignment information. Null is returned if
     * the role is not assigned to the user.
     */
    public function getAssignment($roleName, $userId)
    {
        // TODO: Implement getAssignment() method.
    }

    /**
     * Returns all role assignment information for the specified user.
     * @param string|integer $userId the user ID (see [[\yii\web\User::id]])
     * @return Assignment[] the assignments indexed by role names. An empty array will be
     * returned if there is no role assigned to the user.
     */
    public function getAssignments($userId)
    {
        // TODO: Implement getAssignments() method.
    }

    /**
     * Removes all authorization data, including roles, permissions, rules, and assignments.
     */
    public function removeAll()
    {
        // TODO: Implement removeAll() method.
    }

    /**
     * Removes all permissions.
     * All parent child relations will be adjusted accordingly.
     */
    public function removeAllPermissions()
    {
        // TODO: Implement removeAllPermissions() method.
    }

    /**
     * Removes all roles.
     * All parent child relations will be adjusted accordingly.
     */
    public function removeAllRoles()
    {
        // TODO: Implement removeAllRoles() method.
    }

    /**
     * Removes all rules.
     * All roles and permissions which have rules will be adjusted accordingly.
     */
    public function removeAllRules()
    {
        // TODO: Implement removeAllRules() method.
    }

    /**
     * Removes all role assignments.
     */
    public function removeAllAssignments()
    {
        // TODO: Implement removeAllAssignments() method.
    }
}