<?php

namespace App\Http\Controllers\Admin;

use App\Components\Core\Result;
use App\Components\User\Models\DayOff;
use App\Components\User\Repositories\DayOffRepository;
use Illuminate\Http\Request;

class DayOffController extends AdminController
{
    /**
     * @var DayOffRepository
     */
    private $dayoffRepository;

    /**
     * DayOffController constructor.
     * @param DayOffRepository $dayoffRepository
     */
    public function __construct(DayOffRepository $dayoffRepository)
    {
        $this->dayoffRepository = $dayoffRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->dayoffRepository->index(request()->all());

        return $this->sendResponseOk($data,"get dayoffs ok.");
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
            'start_date' => 'required',
            'type' => 'required',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $dayoff = $this->dayoffRepository->create($request->all());

        if(!$dayoff) return $this->sendResponseBadRequest("Failed to create");

        return $this->sendResponseCreated($dayoff);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dayoff = $this->dayoffRepository->find($id);

        if(!$dayoff) return $this->sendResponseNotFound();

        return $this->sendResponseOk($dayoff);
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
            'start_date' => 'required',
            'type' => 'required',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $updated = $this->dayoffRepository->update($id,$request->all());

        if(!$updated) return $this->sendResponseBadRequest("Failed update.");

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
        /** @var DayOff $dayoff */
        $dayoff = $this->dayoffRepository->find($id);

        if(!$dayoff) return $this->sendResponseNotFound();

        $this->dayoffRepository->delete($id);

        return $this->sendResponseDeleted();
    }
}