<?php namespace App\Services\Annotation;

/**
 * Class Permission
 * @package App\Services\Annotation
 */
class Permission extends Annotation
{

    /**
     * Property to store current annotation in.
     *
     * @var string
     */
    protected $annotation = '@permission';

    /**
     * Gets permission from method.
     *
     * @param $method
     * @param bool $optional
     *
     * @return array|string
     */
    public function getPermission($method, $optional = false)
    {
        $permission = $this->getValues($method);
        if ($permission != '') {
            $permission = explode(':', $permission);
            if ($optional) {
                if (isset($permission[1]) && strtolower($permission[1]) == 'optional') {
                    $permission = '';
                }
            } else {
                $permission = $permission[0];
            }
        }

        return $permission;
    }

    /**
     * Returns current permissions.
     * @return array|string
     */
    public function getPermissions()
    {
        return $this->getValues();
    }
}
