<?php

namespace App\Http\Controllers\Api\v1\Config;

use App\Entities\v1\Config\ConfStatus;
use App\Http\Controllers\Controller;
use App\Transformers\V1\Config\ConfStatusTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

class ConfStatusController extends Controller
{
    use Helpers;
    protected $model;

    public function __construct(ConfStatus $model)
    {
        $this->model = $model;
        $this->middleware('permission:List config status')->only('index');
        $this->middleware('permission:List config status')->only('show');
        $this->middleware('permission:Create config status')->only('store');
        $this->middleware('permission:Update config status')->only('update');
        $this->middleware('permission:Delete config status')->only('destroy');
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
        return $this->response->paginator($paginator, new ConfStatusTransformer());
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
        $ConfStatus = $this->model->create($request->all());
        return $this->response->created(url('api/ConfStatus/' . $ConfStatus->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Entities\v1\Config\ConfStatus  $ConfStatus
     * @return \Illuminate\Http\Response
     */
    public function show(ConfStatus $ConfStatus)
    {
        //
        return $this->response->item($ConfStatus, new ConfStatusTransformer());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\v1\Config\ConfStatus  $ConfStatus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConfStatus $ConfStatus)
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
        $ConfStatus->update($request->except('created_by'));
        return $this->response->item($ConfStatus->fresh(), new ConfStatusTransformer());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Entities\v1\Config\ConfStatus  $ConfStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConfStatus $ConfStatus)
    {
        //
        $record = $this->model->findOrFail($ConfStatus->id);
        $record->delete();
        return $this->response->noContent();
    }
}
