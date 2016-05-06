<?php namespace App\Models;

/**
 * Class Tag
 * @package App\Models
 */
class Tag extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * Array with fields that user are allowed to fill.
     *
     * @var array
     */
    protected $fillable = array('name');

    /**
     * Array with fields that are guarded.
     *
     * @var array
     */
    protected $guarded = array('id');

    /**
     * Array with hidden fields for user.
     *
     * @var array
     */
    protected $hidden = array('pivot', 'updated_at');

    /**
     * Array with rules for fields.
     *
     * @var array
     */
    public static $rules = array(
        'name' => 'required|min:3|unique:tags,name,:id:',
    );

    /**
     * Array with models to reload on save.
     *
     * @var array
     */
    protected $modelsToReload = ['App\Models\Post'];

    /**
     * Fetch posts this tag belongs to many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Posts()
    {
        return $this->belongsToMany('App\Models\Post', 'post_tag');
    }

    /**
     * Fetch hateoas link for api.
     *
     * @return array
     */
    public function getlinksAttribute()
    {
        return $this->hateoas($this->id, 'tags');
    }
}
