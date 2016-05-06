<?php namespace App\Models;

/**
 * Class Permission
 * @package App\Models
 */
class Permission extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * Array with fields that user are allowed to fill.
     *
     * @var array
     */
    protected $fillable = array('permission');

    /**
     * Array with fields that are guarded.
     *
     * @var array
     */
    protected $guarded = array('id');

    /**
     * Array with rules for fields.
     *
     * @var array
     */
    public static $rules = array(
        'permission' => 'required|alpha_dash|unique:permissions,permission',
    );

    /**
     * Array with models to reload on save.
     *
     * @var array
     */
    protected $modelsToReload = ['App\Models\Role'];

    /**
     * Fetch roles this permission belongs to many of.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role');
    }

    /**
     * Fetch permissions name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->permission));
    }

    /**
     * Appends an array of attributes on model.
     *
     * @var array
     */
    protected $appends = array('name');
}
