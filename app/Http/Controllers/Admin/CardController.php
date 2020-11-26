<?php

namespace App\Http\Controllers\Admin;

use App\Components\Card\Repositories\CardRepository;
use Illuminate\Http\Request;

class CardController extends AdminController
{
    /**
     * @var CardRepository
     */
    private $cardRepository;

    /**
     * CardController constructor.
     * @param CardRepository $cardRepository
     */
    public function __construct(CardRepository $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->cardRepository->index(request()->all());

        return $this->sendResponseOk($data,"Get card logins ok.");
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
            'key' => 'required',
            'user_id' => 'required',
            'branch_id' => 'required',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        /** @var Branch $branch */
        $branch = $this->cardRepository->create($request->all());

        if(!$branch) return $this->sendResponseBadRequest("Failed create.");
        
        return $this->sendResponseCreated($branch);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $key
     * @return \Illuminate\Http\Response
     */
    public function show($key)
    {
        $card = $this->cardRepository->getFromKey($key);

        if(!$card) return $this->sendResponseNotFound();

        return $this->sendResponseOk($card);
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
            'key' => 'required',
            'branch_id' => 'required',
            'user_id' => 'required',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $payload = $request->all();

        $updated = $this->cardRepository->update($id,$payload);

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
            $this->cardRepository->delete($id);
        } catch (\Exception $e) {
            return $this->sendResponseBadRequest("Failed to delete");
        }

        return $this->sendResponseDeleted();
    }

}
