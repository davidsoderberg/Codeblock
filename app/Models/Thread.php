<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Thread
 * @package App\Models
 */
class Thread extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'threads';

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['subject'];

    /**
     * Validation rules.
     *
     * @var array
     */
    protected static $rules = [
        'subject' => 'required',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Messages relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany('App\Models\Message');
    }

    /**
     * Returns the latest message from a thread
     *
     * @return \App\Models\Message
     */
    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Participants relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany('App\Models\Participant')->whereNull('participants.deleted_at');
    }

    /**
     * Trashed participants relationship.
     *
     * @return mixed
     */
    public function trashedParticipants()
    {
        return $this->hasMany('App\Models\Participant')->whereNotNull('participants.deleted_at');
    }

    /**
     * Returns the user object that created the thread
     *
     * @return mixed
     */
    public function creator()
    {
        return $this->messages()->oldest()->first()->user;
    }

    /**
     * Returns all of the latest threads by updated_at date
     *
     * @return mixed
     */
    public static function getAllLatest()
    {
        return self::latest('updated_at');
    }

    /**
     * Returns threads that the user is associated with
     *
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeForUser($query, $userId)
    {
        return $query->join('participants', 'threads.id', '=', 'participants.thread_id')
            ->where('participants.user_id', $userId)
            ->where('participants.deleted_at', null)
            ->select('threads.*');
    }

    /**
     * Returns threads with new messages that the user is associated with
     *
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeForUserWithNewMessages($query, $userId)
    {
        return $query->join('participants', 'threads.id', '=', 'participants.thread_id')
            ->where('participants.user_id', $userId)
            ->whereNull('participants.deleted_at')
            ->where(function ($query) {
                $query->where('threads.updated_at', '>',
                    $this->getConnection()->raw($this->getConnection()->getTablePrefix() . 'participants.last_read'))
                    ->orWhereNull('participants.last_read');
            })
            ->select('threads.*');
    }

    /**
     * Returns threads between given user ids
     *
     * @param $query
     * @param $participants
     * @return mixed
     */
    public function scopeBetween($query, array $participants)
    {
        $query->whereHas('participants', function ($query) use ($participants) {
            $query->whereIn('user_id', $participants)
                ->groupBy('thread_id')
                ->havingRaw('COUNT(thread_id)=' . count($participants));
        });
    }

    /**
     * Generates a string of participant information
     *
     * @param null $userId
     * @param array $columns
     * @return string
     */
    public function participantsString($userId = null, $columns = ['name'])
    {
        $columnString = implode(", ' ', " . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . ".",
            $columns);
        $selectString = "concat(" . $this->getConnection()->getTablePrefix() . $this->getUsersTable() . "." . $columnString . ") as name";

        $participantNames = $this->getConnection()->table($this->getUsersTable())
            ->join('participants', $this->getUsersTable() . '.id', '=', 'participants.user_id')
            ->where('participants.thread_id', $this->id)
            ->select($this->getConnection()->raw($selectString));

        if ($userId !== null) {
            $participantNames->where($this->getUsersTable() . '.id', '!=', $userId);
        }

        $userNames = $participantNames->lists($this->getUsersTable() . '.name');

        return implode(', ', $userNames);
    }
}
