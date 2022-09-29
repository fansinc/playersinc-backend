<?php

namespace App\Http\Controllers\Api\v1\Config;

use App\Entities\v1\Config\ConfPaymentMode;
use App\Http\Controllers\Controller;
use App\Transformers\V1\Config\ConfPaymentModeTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

class ConfPaymentModeController extends Controller
{
    use Helpers;
    protected $model;

    public function __construct(ConfPaymentMode $model)
    {
        $this->model = $model;
        $this->middleware('permission:List config payment mode')->only('index');
        $this->middleware('permission:List config payment mode')->only('show');
        $this->middleware('permission:Create config payment mode')->only('store');
        $this->middleware('permission:Update config payment mode')->only('update');
        $this->middleware('permission:Delete config payment mode')->only('destroy');
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
        return $this->response->paginator($paginator, new ConfPaymentModeTransformer());
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
        $ConfPaymentMode = $this->model->create($request->all());
        return $this->response->created(url('api/ConfPaymentMode/' . $ConfPaymentMode->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Entities\v1\Config\ConfPaymentMode  $ConfPaymentMode
     * @return \Illuminate\Http\Response
     */
    public function show(ConfPaymentMode $ConfPaymentMode)
    {
        //
        return $this->response->item($ConfPaymentMode, new ConfPaymentModeTransformer());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\v1\Config\ConfPaymentMode  $ConfPaymentMode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConfPaymentMode $ConfPaymentMode)
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
        $ConfPaymentMode->update($request->except('created_by'));
        return $this->response->item($ConfPaymentMode->fresh(), new ConfPaymentModeTransformer());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Entities\v1\Config\ConfPaymentMode  $ConfPaymentMode
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConfPaymentMode $ConfPaymentMode)
    {
        //
        $record = $this->model->findOrFail($ConfPaymentMode->id);
        $record->delete();
        return $this->response->noContent();
    }
}
