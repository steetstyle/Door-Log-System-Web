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
        if($params['key']){
            $query = $query->where('key','=', $params['key']);
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