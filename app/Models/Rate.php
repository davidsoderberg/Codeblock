<?php namespace App\Models;

/**
 * Class Rate
 * @package App\Models
 */
class Rate extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rates';

    /**
     * Array with rules for fields.
     *
     * @var array
     */
    public static $rules = array();

    /**
     * Array with models to reload on save.
     *
     * @var array
     */
    protected $modelsToReload = ['App\Models\Comment'];

    /**
     * Array with fields that user are allowed to fill.
     *
     * @var array
     */
    protected $fillable = array('user_id', 'comment_id', 'type');
}
