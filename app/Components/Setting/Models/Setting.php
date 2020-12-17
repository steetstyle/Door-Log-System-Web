<?php

namespace App\Components\Setting\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Branch
 * @package App\Components\Branch\Models
 *
 * @property int $id
 * @property time $login_time
 * @property time $max_late_time
 * @property time $exit_time
 */
class Setting extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['login_time', 'max_late_time'];


}
