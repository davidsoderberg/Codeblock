<?php namespace App\Models;

use App\Models\Traits\TeamTrait;
use Illuminate\Support\Facades\Auth;

/**
 * Class Team
 * @package App\Models
 */
class Team extends Model
{
    use TeamTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'teams';

    /**
     * Array with fields that user are allowed to fill.
     *
     * @var array
     */
    protected $fillable = ['name', 'owner_id'];

    /**
     * Array with fields that are guarded.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Array with rules for fields.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|min:3|unique:teams,name,:id:',
        'owner_id' => 'integer',
    ];
}
