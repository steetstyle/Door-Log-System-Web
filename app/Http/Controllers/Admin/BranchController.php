<?php

namespace App\Http\Controllers\Admin;

use App\Components\Branch\Repositories\BranchRepository;
use Illuminate\Http\Request;

class BranchController extends AdminController
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
        $data = $this->branchRepository->index(request()->all());

        return $this->sendResponseOk($data,"Get branches ok.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = validator($request->all(),[
            'name' => 'required',
            'tag' => 'required',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        /** @var Branch $branch */
        $branch = $this->branchRepository->create($request->all());

        if(!$branch) return $this->sendResponseBadRequest("Failed create.");
        
        return $this->sendResponseCreated($branch);
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $branch = $this->branchRepository->find($id);

        if(!$branch) return $this->sendResponseNotFound();

        return $this->sendResponseOk($branch);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = validator($request->all(),[
            'name' => 'required',
            'tag' => 'required',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $payload = $request->all();

        $updated = $this->branchRepository->update($id,$payload);

        if(!$updated) return $this->sendResponseBadRequest("Failed update");

        return $this->sendResponseUpdated();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try {
            $this->branchRepository->delete($id);
        } catch (\Exception $e) {
            return $this->sendResponseBadRequest("Failed to delete");
        }

        return $this->sendResponseDeleted();
    }



}
