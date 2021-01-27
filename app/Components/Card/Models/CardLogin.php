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
class CardLogin extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'card_login';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'branch_id', 'updated_at', 'user_id'];

    protected $appends = ['card', 'branch', 'user'];


    /**
     * returns the Card on this Card Login
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function card()
    {
        return $this->belongsTo(Card::class, 'key', 'key');
    }

    /**
     * returns the Logged User on this Card Login
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function logged_user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

     /**
     * returns the Branch on this Card Login
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id', 'id');
    }

    public function getCardAttribute(){
        return $this->card()->first();
    }

    public function getBranchAttribute(){
        return $this->branch()->first();
    }

    public function getUserAttribute(){
        
        $query =  $this->card()->first();

        $query2 = null;

        if($query != null) $query = $query->user()->first();
        else $query2 = $this->logged_user()->first();

        return $query != null ? $query : ($query2 != null ? $query2 : null);
    }
   
}
