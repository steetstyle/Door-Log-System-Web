<?php

namespace App\Components\Branch\Models;

use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BranchUser
 * @package App\Components\Branch\Models
 *
 * @property int $id
 * @property string $name
 */
class BranchUser extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'branch_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'branch_id'];

}
