<?php
/**
 * Created by manual.
 * User: furkan bostancı
 * Date: 24/11/2020
 * Time: 14:38 PM
 */

namespace App\Components\Card\Repositories;


use App\Components\User\Models\User;

use App\Components\Core\BaseRepository;
use App\Components\Card\Models\CardLogin;
use App\Components\Setting\Models\Setting;
use App\Components\Branch\Models\Branch;
use App\Components\Core\Utilities\Helpers;
use Illuminate\Support\Arr;

class CardLoginRepository extends BaseRepository
{
    public function __construct(CardLogin $model)
    {
        parent::__construct($model);
    }

    /**
     * index items
     *
     * @param array $params
     * @return CardLoginRepository[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function index($params)
    {

        $query = CardLogin::where('card_login.key', '!=' , null)->orderBy('card_login.id', 'DESC');
        if(array_key_exists('start_date', $params) && $params['start_date']){
            if(key_exists('end_date', $params) && $params['end_date']){

                $query = $query->whereBetween('card_login.created_at', [$params['start_date']." 00:00:00.000000",$params['end_date']." 23:59:59.000000"]);
            }
            else{
                $query = $query->whereBetween('card_login.created_at', [$params['start_date']." 00:00:00.000000",$params['start_date']." 23:59:59.000000"]);
            }
        }

        if(array_key_exists('branchName', $params) && $params['branchName']){
            $query = $query->join('branches as abranch', 'card_login.branch_id', '=', 'abranch.id')
                            ->where('abranch.name', '=', $params['branchName']);
        }

        if(array_key_exists('key', $params) && $params['key']){
            $query = $query->where('card_login.key', '=', $params['key']);
        }
        
        if(array_key_exists('name', $params) && $params['name']){
            $query = $query->join('card as acard', 'card_login.key', '=', 'acard.key')
                            ->join('users', 'users.id', '=', 'acard.user_id')
                            ->where('users.name', '=', $params['name']);
        }
       
        return $query->get();
    }

    /**
     * create device log
     *
     * @param array $params
     * @return CardLogin
     */
    public function add_device_log(array $params){

        $branch = Branch::where('name', '=', $params['place'])->first();
        if(!$branch) return false;

        $payload = [];
        $setting = Setting::all()->first();
        if($setting == null){
            $setting = Setting::create();
        }

        $payload['branch_id'] = $branch->id;
        $payload['key'] = $params['key'];
        $payload['login_time'] = $setting->login_time;
        $payload['max_late_time'] = $setting->max_late_time;
        $payload['exit_time'] = $setting->exit_time;
        $cardLogin = $this->create($payload);
        
        return $cardLogin;

    }

}