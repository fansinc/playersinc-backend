<?php

namespace App\Http\Controllers\Api\v1\Config;

use App\Entities\v1\Config\ConfPaymentStatus;
use App\Http\Controllers\Controller;
use App\Transformers\V1\Config\ConfPaymentStatusTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

class ConfPaymentStatusController extends Controller
{
    use Helpers;
    protected $model;

    public function __construct(ConfPaymentStatus $model)
    {
        $this->model = $model;
        $this->middleware('permission:List config payment status')->only('index');
        $this->middleware('permission:List config payment status')->only('show');
        $this->middleware('permission:Create config payment status')->only('store');
        $this->middleware('permission:Update config payment status')->only('update');
        $this->middleware('permission:Delete config payment status')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $paginator = $this->model->orderBy('created_at', 'desc')->paginate($request->get('limit', config('app.pagination_limit')));
        if ($request->has('limit')) {
            $paginator->appends('limit', $request->get('limit'));
        }
        return $this->response->paginator($paginator, new ConfPaymentStatusTransformer());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request['created_by'] = $request->user()->id;
        $request['updated_by'] = $request->user()->id;
        $rules = [

            'name' => 'required|string',

        ];
        $this->validate($request, $rules);
        $ConfPaymentStatus = $this->model->create($request->all());
        return $this->response->created(url('api/ConfPaymentStatus/' . $ConfPaymentStatus->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Entities\v1\Config\ConfPaymentStatus  $ConfPaymentStatus
     * @return \Illuminate\Http\Response
     */
    public function show(ConfPaymentStatus $ConfPaymentStatus)
    {
        //
        return $this->response->item($ConfPaymentStatus, new ConfPaymentStatusTransformer());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\v1\Config\ConfPaymentStatus  $ConfPaymentStatus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConfPaymentStatus $ConfPaymentStatus)
    {
        //
        $request['updated_by'] = $request->user()->id;
        $rules = [
            'name' => 'required|string',
        ];
        if ($request->method() == 'PATCH') {
            $rules = [
                'name' => 'sometimes|required|string',
            ];
        }

        $this->validate($request, $rules);       
        $ConfPaymentStatus->update($request->except('created_by'));
        return $this->response->item($ConfPaymentStatus->fresh(), new ConfPaymentStatusTransformer());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Entities\v1\Config\ConfPaymentStatus  $ConfPaymentStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConfPaymentStatus $ConfPaymentStatus)
    {
        //
        $record = $this->model->findOrFail($ConfPaymentStatus->id);
        $record->delete();
        return $this->response->noContent();
    }
}
