<?php
/**
 * Created by manual.
 * User: furkan bostancı
 * Date: 24/11/2020
 * Time: 14:38 PM
 */

namespace App\Components\Branch\Repositories;


use App\Components\Branch\Models\BranchUser;

use App\Components\Core\BaseRepository;
use App\Components\Branch\Models\Branch;
use App\Components\Core\Utilities\Helpers;
use Illuminate\Support\Arr;

class BranchRepository extends BaseRepository
{
    public function __construct(Branch $model)
    {
        parent::__construct($model);
    }

    /**
     * index items
     *
     * @param array $params
     * @return BranchRepository[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function index($params)
    {
        return $this->get($params,[],function($q) use ($params)
        {
            $name = Arr::get($params,'name','');

            $q->where('name','like',"%{$name}%");

            return $q;
        });
    }


    /**
     * Create User For Branch
     * @param array $data
     */
    public function createBranchUserForBranch(array $data){
        $branchUser =  BranchUser::where('branch_id', '=', $data['branch_id'])
                                    ->where('user_id', '=', $data['user_id'])->first();
        if($branchUser == null){
            $branchUser = BranchUser::create($data);
            $branchUser = $branchUser->join('users', 'user_id', '=', 'users.id');
        }
        
        return $branchUser;
    }


     /**
     * delete a user by id
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
            /** @var Branch $Branch */
            $Branch = $this->model->find($id);

            if(!$Branch)
            {
                return false;
            };

            $Branch->delete();
        }

        return true;
    }


    public function delete_branch_user(int $user_id, int $branch_id){

        $branch_user = BranchUser::where('branch_id', '=', $branch_id)
        ->where('user_id', '=', $user_id)->first();

        if(!$branch_user)
        {
            return false;
        };

        $branch_user->delete();

        return true;
    }

    
}