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
            'name' => 'required',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        /** @var Branch $branch */
        $branch = $this->cardLoginRepository->create($request->all());

        if(!$branch) return $this->sendResponseBadRequest("Failed create.");
        
        return $this->sendResponseCreated($branch);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add_device_log(){
        $validate = validator($request->all(),[
            'key' => 'required',
            'place' => 'required',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());
        
        /** @var CardLogin $cardLogin */
        $cardLogin = $this->cardLoginRepository->add_device_log($request->all());

        if(!$branch) return $this->sendResponseBadRequest("Failed create.");
        
        return $this->sendResponseCreated($branch);
    }
}
