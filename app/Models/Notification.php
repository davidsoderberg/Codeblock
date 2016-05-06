<?php namespace App\Models;

/**
 * Class Notification
 * @package App\Models
 */
class Notification extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * Array with fields that user are allowed to fill.
     *
     * @var array
     */
    protected $fillable = array(
        'user_id',
        'type',
        'subject',
        'body',
        'object_id',
        'object_type',
        'from_id'
    );

    /**
     * Array with fields to add to hidden array.
     *
     * @var array
     */
    protected $addHidden = array('object_id', 'object_type');

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
        'user_id' => 'required|integer',
        'type' => 'required|min:3',
        'subject' => 'required|min:3',
        'body' => 'required|min:3',
        'object_id' => 'integer',
        'object_type' => 'min:3',
        'from_id' => 'required|integer',
    );

    /**
     * Array with models to reload on save.
     *
     * @var array
     */
    protected $modelsToReload = ['App\Models\User'];

    /**
     * Fetch sending user this notification has one of.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sender()
    {
        return $this->hasOne('App\Models\User', 'id', 'from_id');
    }

    /**
     * Fetch receiving user this notification has one of.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function receiver()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    /**
     * Fetch model object this notification has one of.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function object()
    {
        return $this->hasOne('App\\Models\\' . $this->object_type, 'id', 'object_id');
    }
}
