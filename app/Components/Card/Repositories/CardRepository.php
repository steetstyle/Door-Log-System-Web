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
use App\Components\Card\Models\Card;
use App\Components\Core\Utilities\Helpers;
use Illuminate\Support\Arr;

class CardRepository extends BaseRepository
{
    public function __construct(Card $model)
    {
        parent::__construct($model);
    }

    /**
     * index items
     *
     * @param array $params
     * @return CardRepository[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function index($params)
    {
        
        $query = Card::where('card.key', '!=' , null)->orderBy('card.id', 'DESC');
        if($params != null ){
            if(array_key_exists('key',$params) && !empty($params['key'])){
                $query = $query->where('key','=', $params['key']);
            }
            if(array_key_exists('user_id',$params) && !empty($params['user_id'])){
                $query = $query->where('user_id','=', $params['user_id']);
            }
            if(array_key_exists('branch_id',$params) && !empty($params['branch_id'])){
                $query = $query->where('branch_id','=', $params['branch_id']);
            }
            if(array_key_exists('name',$params) && !empty($params['name'])){
                $query = $query->join('users', 'users.id', '=', 'card.user_id')
                ->where('users.name', 'LIKE', "%".$params['name']."%");
             
            }
        }
        return $query->get();
    }

    public function getFromKey($key){
        return Card::where('key', '=', $key)->first();
    }

     /**
     * delete a Card by id
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id)
    {
        $ids = explode(',',$id);

        foreach ($ids as $id)
        {
            /** @var Card $Card */
            $Card = $this->model->find($id);

            if(!$Card)
            {
                return false;
            };

            $Card->delete();
        }

        return true;
    }
    
}