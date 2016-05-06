<?php namespace App\Models;

/**
 * Class Article
 * @package App\Models
 */
class Article extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'articles';

    /**
     * Array with fields that user are allowed to fill.
     *
     * @var array
     */
    protected $fillable = array('title', 'body', 'slug');

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
        'title' => 'required|unique:articles,title,:id:',
        'body' => 'required|min:3',
        'slug' => 'required|min:3|unique:articles,slug,:id:',
    );
}
