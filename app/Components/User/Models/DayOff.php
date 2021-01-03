<?php

namespace App\Components\User\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DayOff
 * @package App\Components\User\Models
 *
 * @property int $id
 * @property DateTime $title
 * @property DateTime $title
 * @property string $description
 * @property int $user_id
 */
class DayOff extends Model
{

    const DAYOFF_NORMAL 	    = 'normal';
    const DAYOFF_OFFICAL 	    = 'offical';
   

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dayoff';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['start_date', 'end_date', 'user_id', 'type' ,'description'];

    /**
     * the rules of the Dayoff for validation before persisting
     *
     * @var array
     */
    public static $rules = array(
        'start_date' => 'required',
        'type' => 'required',
    );
}
