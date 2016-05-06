<?php namespace App\Models;

/**
 * Class read
 * @package App\Models
 */
class Read extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reads';

    /**
     * Array with fields that user are allowed to fill.
     *
     * @var array
     */
    protected $fillable = array('user_id', 'topic_id');

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
        'topic_id' => 'required|integer',
    );

    /**
     * Array with models to reload on save.
     *
     * @var array
     */
    protected $modelsToReload = ['App\Models\User', 'App\Models\Topic'];
}
