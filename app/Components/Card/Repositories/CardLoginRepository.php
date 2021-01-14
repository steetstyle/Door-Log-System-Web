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
use App\Components\Card\Models\Card;
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
    public function index($params, $selectArray = ["*", 'card_login.updated_at as log', "card_login.id as id", 'card_login.key as key', 'card_login.created_at as created_at', 'card_login.updated_at as updated_at', 'card_login.branch_id as branch_id'])
    {
        $query = CardLogin::where('card_login.key', '!=' , null);


        if(array_key_exists('start_date', $params) && $params['start_date']){
            if(key_exists('end_date', $params) && $params['end_date']){

                $query = $query->whereBetween('card_login.updated_at', [$params['start_date']." 00:00:00.000000",$params['end_date']." 23:59:59.000000"]);
            }
            else{
                $query = $query->whereBetween('card_login.updated_at', [$params['start_date']." 00:00:00.000000",$params['start_date']." 23:59:59.000000"]);
            }
        }

        if(array_key_exists('branchName', $params) && $params['branchName']){
            $query = $query->join('branches as abranch', 'card_login.branch_id', '=', 'abranch.id')
                            ->where('abranch.tag', '=', $params['branchName']);
            $query = $query->select('abranch.tag as branch_name');

        }

        if(array_key_exists('key', $params) && $params['key']){
            $query = $query->where('card_login.key', '=', $params['key']);
        }

        if(array_key_exists('name', $params) || array_key_exists('user_id', $params)){
            $query = $query
            ->leftJoin('card as acard', 'card_login.key', '=', 'acard.key')
            ->leftJoin('users', 'users.id', '=', 'acard.user_id');
                            
            if(array_key_exists('name', $params)  && !empty($params['name'])){
                $query = $query->where('users.name', '=', $params['name']);
            }

            if(array_key_exists('user_id', $params)  && !empty($params['user_id'])){
                $query = $query->where('users.id', '=', $params['user_id']);

            }

            $query = $query->select('users.id as user_id', 'users.first_name as first_name', 'users.last_name as last_name');

        }


        $query = $query->select($selectArray)->orderBy('card_login.id', 'DESC');

        $page_params = [
            'page' => array_key_exists('page', $params) ?  $params['page'] -1 : 0,
            'per_page' =>  array_key_exists('per_page', $params) ?  $params['per_page'] : 10,
        ];

        return $query->get()->skip($page_params['page']*$page_params['per_page'])->take($page_params['per_page']);
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

        /*   $card = Card::where('branch_id', '=',  $branch->id)
                                ->where('key', '=', $params['key'])->first();

                if($card != null) {
                    $payload['user_id'] = $card->user_id;
                }
        */
        
        $payload['branch_id'] = $branch->id;
        $payload['key'] = $params['key'];
        $payload['login_time'] = $setting->login_time;
        $payload['max_late_time'] = $setting->max_late_time;
        $payload['exit_time'] = $setting->exit_time;
        $cardLogin = $this->create($payload);
        
        return $cardLogin;

    }

}