<?php

namespace App\Http\Controllers\Admin;

use App\Components\Card\Repositories\CardLoginRepository;
use App\Components\Card\Models\CardLogin;
use Illuminate\Http\Request;

class CardLoginController extends AdminController
{
    /**
     * @var CardLoginRepository
     */
    private $cardLoginRepository;

    /**
     * CardLoginController constructor.
     * @param CardLoginRepository $cardLoginRepository
     */
    public function __construct(CardLoginRepository $cardLoginRepository)
    {
        $this->cardLoginRepository = $cardLoginRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->cardLoginRepository->index(request()->all());

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
            'updated_at' => 'required',
            'branch_id' => 'required',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        /** @var CardLogin $cardLogin */
        $cardLogin = $this->cardLoginRepository->create($request->only('key', 'updated_at', 'branch_id'));

        if(!$cardLogin) return $this->sendResponseBadRequest("Failed create.");
        
        return $this->sendResponseCreated($cardLogin);
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $card = $this->cardLoginRepository->find($id);

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
            'updated_at' => 'required',
            'branch_id' => 'required',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $payload = $request->only('key', 'updated_at', 'branch_id');

        $updated = $this->cardLoginRepository->update($id,$payload);

        if(!$updated) return $this->sendResponseBadRequest("Failed update");

        return $this->sendResponseUpdated();
    }

    


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add_device_log(Request $request){
        $validate = validator($request->all(),[
            'key' => 'required',
            'place' => 'required',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());
        
        /** @var CardLogin $cardLogin */
        $cardLogin = $this->cardLoginRepository->add_device_log($request->all());

        if(!$cardLogin) return $this->sendResponseBadRequest("Failed create.");
        
        return $this->sendResponseCreated($cardLogin);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /** @var CardLogin $cardLogin */
        $cardLogin = $this->cardLoginRepository->find($id);

        if(!$cardLogin) return $this->sendResponseNotFound();

        $this->cardLoginRepository->delete($id);

        return $this->sendResponseDeleted();
    }
}
