<?php

namespace App\Http\Controllers\Admin;

use App\Components\Branch\Repositories\BranchRepository;
use Illuminate\Http\Request;

class BranchUserController extends AdminController
{
    /**
     * @var BranchRepository
     */
    private $branchRepository;

    /**
     * BranchController constructor.
     * @param BranchRepository $branchRepository
     */
    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branchUsers = $this->branchRepository->find($id)->users();

        if(!$branchUsers) return $this->sendResponseNotFound();
        dd($branchUsers);
        return $this->sendResponseOk($branchUsers);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_and_branch_id)
    {
        $params = explode(',', $user_and_branch_id);
        try {
            $this->branchRepository->delete_branch_user($params[0],$params[1]);
        } catch (\Exception $e) {
            return $this->sendResponseBadRequest("Failed to delete");
        }

        return $this->sendResponseDeleted();
    }

        /**
     * add user to branch
     *
     * @return JSON
     */
    public function store(Request $request)
    {
        $validate = validator($request->all(),[
            'user_id' => 'required',
            'branch_id' => 'required',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());
         
        /** @var Branch $branch */
         $branchUser = $this->branchRepository->createBranchUserForBranch($request->all());

         if(!$branchUser) return $this->sendResponseBadRequest("Failed create.");
         
         return $this->sendResponseCreated($branchUser);
    }


    /**
     * get branch users
     *
     * @return Array
     */
    public function show($id)
    {
        $branchUsers = $this->branchRepository->find($id)->users()->get();

        if(!$branchUsers) return $this->sendResponseNotFound();
        return $this->sendResponseOk($branchUsers);
       
    }

}
