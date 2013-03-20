<?php


/**
 *
 */
class mmInstaller {

    public static $errorMessages = array( );
    public static $infoMessages  = array( );

    private static function logError($msg) {
        self::$errorMessages[] = $msg;
        echo 'ERROR: ' . $msg . '<br/>';

    }


    private static function logInfo($msg) {
        self::$infoMessages[] = $msg;
        echo 'INFO: ' . $msg . '<br/>';

    }


    /**
     * Delete Permission
     *
     * @param string $permissionName
     */
    public static function deletePermission($permissionName) {
        if ( $perm = Permission::findByName($permissionName) ) {

            // unrelate roles assigned to permission
            RolePermission::deleteWhere('RolePermission', 'permission_id=?', array( $perm->id ));

            if ( !$perm->delete() ) {
                self::logError(__('Permission could not be deleted') . ' - ' . $permissionName);
                return false;
            } else {
                self::logInfo(__('Permission deleted') . ' - ' . $permissionName);
                return true;
            }
        } else {
            self::logInfo(__('Permission') . ' ' . $permissionName . ' ' . __('was not found and not deleted!'));
            return true;
        }

    }


    /**
     * Create Permission
     *
     * @param string $permissionName
     */
    public static function createPermission($permissionName) {
        if ( !Permission::findByName($permissionName) ) {
            $perm = new Permission(array( 'name' => $permissionName ));
            if ( !$perm->save() ) {
                self::logError(__('Permission could not be created') . ' - ' . $permissionName);
                return false;
            } else {
                self::logInfo(__('Permission created') . ' - ' . $permissionName);
                return true;
            }
        } else {
            self::logInfo(__('Permission already exists - ' . $permissionName));
            return true;
        }

    }


    /**
     * Delete Role
     *
     * @param string $roleName
     */
    public static function deleteRole($roleName) {
        if ( $role = Role::findByName($roleName) ) {

            if ( Record::existsIn('RolePermission', 'role_id=?', array( $role->id )) ) {
                self::logError(__('Role has some permissions') . ' - ' . $roleName . ' - ' . __('cannot delete role with existing permissions'));
                return false;
            };

            if ( !$role->delete() ) {
                self::logError(__('Role could not be deleted') . ' - ' . $roleName);
                return false;
            } else {
                self::logInfo(__('Role deleted') . ' - ' . $roleName);
                return true;
            }
        } else {
            self::logInfo(__('Role') . ' ' . $roleName . ' ' . __('was not found and not deleted!'));
            return true;
        }

    }


    /**
     * Create Role
     *
     * @param string $roleName
     */
    public static function createRole($roleName) {
        if ( !Role::findByName($roleName) ) {
            $role = new Role(array( 'name' => $roleName ));
            if ( !$role->save() ) {
                self::logError(__('Could not create role - ') . $roleName);
                return false;
            } else {
                self::logInfo(__('Created role - ') . $roleName);
                return true;
            }
        } else {
            self::logInfo(__('Role already exists - ') . $roleName);
            return true;
        }

    }


    /**
     * Assign Permission to Role
     *
     * @global type $errorMessages
     * @global string $infoMessages
     * @param type $permissionName
     * @param type $roleName
     * @return boolean
     */
    public static function assignPermissionToRole($permissionName, $roleName) {

        $perm = Permission::findByName($permissionName);
        $role = Role::findByName($roleName);
        if ( ($role && $perm ) ) {
            if ( Record::existsIn('RolePermission', 'permission_id=? AND role_id=?', array( $perm->id, $role->id )) ) {
                self::logInfo(__('Role') . ' ' . $roleName . ' ' . __('already has permission') . ' ' . $permissionName . '!');
                return true;
            }
            $rp = new RolePermission(array( 'permission_id' => $perm->id, 'role_id'       => $role->id ));
            if ( !$rp->save() ) {
                self::logError(__('Could not assign permission') . ' ' . $permissionName . ' ' . __('to role') . ' ' . $roleName . '!');
                return false;
            }
            else
                self::logInfo(__('Assigned permission') . ' ' . $permissionName . ' ' . __('to role') . ' ' . $roleName . '!');
            return true;
        } else {
            self::logError(__('Either permission or role does not exist - ') . ' ' . $permissionName . ', ' . $roleName . '!');
            return false;
        }

    }


}