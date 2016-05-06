<?php namespace App\Models;

/**
 * Class Topic
 * @package App\Models
 */
class Topic extends Model
{
	/**
	 * Boot method for Topic model.
	 */
	public static function boot()
	{
		parent::boot();
		static::deleting(function ($object) {
			foreach ($object->replies as $reply) {
				$reply->delete();
			}
			foreach ($object->reads as $read) {
				$read->delete();
			}
		});
	}

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'topics';

	/**
	 * Array with fields that user are allowed to fill.
	 *
	 * @var array
	 */
	protected $fillable = ['title', 'forum_id'];

	/**
	 * Array with fields that are guarded.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];

	/**
	 *  Array with hidden fields for user.
	 *
	 * @var array
	 */
	protected $hidden = ['forum', 'updated_at'];

	/**
	 * Array with related models that should be eagerloaded.
	 *
	 * @var array
	 */
	protected $with = ['replies'];

	/**
	 * Array with rules for fields.
	 *
	 * @var array
	 */
	public static $rules = [
		'title' => 'required|min:3',
		'forum_id' => 'required|integer',
	];

	/**
	 * Array with models to reload on save.
	 *
	 * @var array
	 */
	protected $modelsToReload = ['App\Models\Reply', 'App\Models\Forum', 'App\Models\Read'];

	/**
	 * Fetch replies this topic has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function replies()
	{
		return $this->hasMany('App\Models\Reply', 'topic_id', 'id');
	}

	/**
	 * Fetch forum this topic belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function forum()
	{
		return $this->belongsTo('App\Models\Forum', 'forum_id');
	}

	/**
	 * Fetch reads this topic has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function reads()
	{
		return $this->hasMany('App\Models\Read', 'topic_id', 'id');
	}

	/**
	 * Fetch this topics forum title.
	 *
	 * @return string
	 */
	public function getforumtitleAttribute()
	{
		if ($this->forum) {
			return $this->forum->title;
		}

		return "";
	}

	/**
	 * Fetch hateoas link for api.
	 *
	 * @return array
	 */
	public function getlinksAttribute()
	{
		return $this->hateoas($this->id, 'topics');
	}

	/**
	 * Appends an array of attributes on model.
	 *
	 * @var array
	 */
	protected $appends = ['forumtitle'];
}
