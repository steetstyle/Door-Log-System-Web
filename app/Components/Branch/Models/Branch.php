<?php

namespace App\Components\Branch\Models;

use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Branch
 * @package App\Components\Branch\Models
 *
 * @property int $id
 * @property string $name
 */
class Branch extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'branches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * returns the users on this branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class,'branch_user','branch_id');
    }
}
