<?php namespace App\Repositories\Read;

use App\Models\Read;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;
use App\Services\CollectionService;

/**
 * Class EloquentReadRepository
 * @package App\Repositories\Read
 */
class EloquentReadRepository extends CRepository implements ReadRepository
{

    /**
     * Fetch all reads.
     *
     * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null
     */
    private function get()
    {
        return $this->cache('all', Read::where('id', '!=', 0));
    }


    /**
     * Update read so topic can be rendered as read.
     *
     * @param $topic_id
     * @param $user_id
     *
     * @return null
     */
    public function hasRead($topic_id, $user_id = null)
    {
        if (is_null($user_id) && Auth::check()) {
            $user_id = Auth::user()->id;
        }

        if ( ! is_null($user_id)) {
            $is_null = CollectionService::filter($this->get(), 'topic_id', $topic_id);
            $is_null = CollectionService::filter($is_null, 'user_id', $user_id, 'first');
            if (is_null($is_null)) {
                $Read           = new Read;
                $Read->topic_id = $topic_id;
                $Read->user_id  = $user_id;
                $Read->save();
            }
        }
    }

    /**
     * Deletes a read.
     *
     * @param $topic_id
     *
     * @return null
     */
    public function UpdatedRead($topic_id)
    {
        foreach (CollectionService::filter($this->get(), 'topic_id', $topic_id) as $Read) {
            $Read->delete();
        }
    }
}
