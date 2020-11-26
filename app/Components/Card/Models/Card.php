<?php

namespace App\Components\Card\Models;

use App\Components\User\Models\User;
use App\Components\Branch\Models\Branch;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Branch
 * @package App\Components\Branch\Models
 *
 * @property int $id
 * @property string $name
 */
class Card extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'card';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'user_id', 'branch_id'];

    protected $appends = ['user', 'branch'];

    /**
     * returns the users on this branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    /**
     * returns the users on this branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id','id');
    }

    public function getuserAttribute(){
        return $this->user()->first();
    }

    public function getBranchAttribute(){
        return $this->branch()->first();
    }
}
