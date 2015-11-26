<?php namespace App\Models;

/**
 * Class Post
 * @package App\Models
 */
class Post extends Model {

	/**
	 * Boot method for model.
	 */
	public static function boot() {
		parent::boot();
		static::deleting( function ( $object ) {
			if ( !empty( $object->tags[0] ) ) {
				$object->tags()->detach();
			}
			foreach( $object->stars as $star ) {
				$star->delete();
			}
			foreach( $object->comments as $comment ) {
				$comment->delete();
			}
		} );
	}

	/**
	 * Array with models to reload on save.
	 *
	 * @var array
	 */
	protected $modelsToReload = ['App\Models\Tag', 'App\Models\Star', 'App\Models\Comment'];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'posts';

	/**
	 * Enables revision for this model.
	 *
	 * @var bool
	 */
	protected $revisionEnabled = true;

	/**
	 * Array with fields that user are allowed to fill.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'cat_id', 'description', 'code', 'user_id', 'org', 'slug', 'team_id'];

	/**
	 * Array with fields that are guarded.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];

	/**
	 * Array with models that should be eagerloaded.
	 *
	 * @var array
	 */
	protected $with = ['tags', 'category', 'user', 'team'];

	/**
	 * Array with rules for fields.
	 *
	 * @var array
	 */
	public static $rules = [
		'name' => 'required|min:3|unique:posts,name,:id:',
		'cat_id' => 'required|integer',
		'description' => 'required|min:3',
		'code' => 'required|min:3',
		'user_id' => 'integer',
		'team_id' => 'integer',
		'slug' => 'required|min:3|unique:posts,slug,:id:',
	];

	/**
	 * Fetch category this post belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function category() {
		return $this->belongsTo( 'App\Models\Category', 'cat_id' );
	}

	/**
	 * Fetch team this post belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function team() {
		return $this->belongsTo( 'App\Models\Team', 'team_id' );
	}

	/**
	 * Fetch tags this post has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function tags() {
		return $this->belongsToMany( 'App\Models\Tag', 'post_tag' );
	}

	/**
	 * Fetch stars this post has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function stars() {
		return $this->hasMany( 'App\Models\Star', 'post_id', 'id' );
	}

	/**
	 * Fetch user this post belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user() {
		return $this->belongsTo( 'App\Models\User', 'user_id' );
	}

	/**
	 * Fetch comments this post has many of.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function comments() {
		return $this->hasMany( 'App\Models\Comment', 'post_id', 'id' );
	}

	/**
	 * Fetch original post this post has been forked from.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function original() {
		return $this->belongsTo( 'App\Models\Post', 'org' );
	}

	/**
	 * Fetch star count for this post.
	 *
	 * @return int
	 */
	public function getstarcountAttribute() {
		return count( $this->stars()->getResults() );
	}

	/**
	 * Checks if user has stared this post.
	 *
	 * @param $user_id
	 *
	 * @return bool
	 */
	public function StaredByUser( $user_id ) {
		$stars = $this->stars()->getResults();
		$userArray = [];
		foreach( $stars as $star ) {
			$userArray[] = $star->user_id;
		}

		return in_array( $user_id, $userArray );
	}

	/**
	 * Fetch this post category name.
	 *
	 * @return mixed
	 */
	public function getcategorynameAttribute() {
		if ( !is_null( $this->category ) ) {
			return $this->category->name;
		}
	}

	/**
	 * Fetch forked from id.
	 *
	 * @return int
	 */
	public function getforkedAttribute() {
		return count( Post::where( 'org', '=', $this->id )->get() );
	}

	/**
	 * Fetch hateoas link for api.
	 *
	 * @return array
	 */
	public function getlinksAttribute() {
		return $this->hateoas( $this->id, 'posts' );
	}

	/**
	 * Appends an array of attributes on model.
	 *
	 * @var array
	 */
	protected $appends = ['categoryname', 'starcount', 'forked'];

}