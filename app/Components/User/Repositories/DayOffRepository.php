<?php
/**
 * Created by PhpStorm.
 * User: furkan
 * Date: 10/6/2017
 * Time: 5:07 PM
 */

namespace App\Components\User\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\User\Models\DayOff;
use Illuminate\Support\Arr;

class DayOffRepository extends BaseRepository
{
    public function __construct(DayOff $model)
    {
        parent::__construct($model);
    }

    /**
     * index items
     *
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Model[]|\Illuminate\Support\Collection
     */
    public function index($params)
    {
        return $this->get($params,[],function($q) use ($params)
        {
            if(array_key_exists('start_date', $params) && !empty($params['start_date'])){
                $q->whereBetween('start_date', [$params['start_date'] ?? '', $params['end_date'] ?? '']);

            }
            if(array_key_exists('end_date', $params) && !empty($params['end_date'])){
                $q->whereBetween('end_date', [$params['start_date'] ?? '', $params['end_date'] ?? '']);

            }
            if(array_key_exists('description', $params)  && !empty($params['description'])){
                $q->where('description', 'like', '%'.$params['description'] ?? ''.'%');

            }
            if(array_key_exists('user_id', $params) && !empty($params['user_id'])){
                $q->where('user_id', '=', $params['user_id'] ?? '');
            }

            return $q;
        });
    }
}