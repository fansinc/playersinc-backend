<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\v1\MyWeek;
use App\Http\Controllers\Controller;
use App\Transformers\V1\MyWeekTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Entities\Assets\Asset;

class MyWeekController extends Controller
{
    use Helpers;
    protected $model;

    public function __construct(MyWeek $model)
    {
        $this->model = $model;
        // $this->middleware('permission:List MyWeek')->only('index');
        // $this->middleware('permission:List MyWeek')->only('show');
        $this->middleware('permission:Create myweek')->only('store');
        $this->middleware('permission:Update myweek')->only('update');
        $this->middleware('permission:Delete myweek')->only('destroy');
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
        return $this->response->paginator($paginator, new MyWeekTransformer());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['created_by'] = $request->user()->id;
        $request['updated_by'] = $request->user()->id;
        $request['status_id'] = 1;

        $rules = [
            'player_id' => 'required|integer|exists:users,id',
            // 'status_id' => 'required|integer|exists:conf_statuses,id',
            'file' => 'array',
            'file.*' => 'image|mimes:jpeg,jpg,png|max:2048',
          
        ];
        $this->validate($request, $rules);
        $MyWeek = $this->model->create($request->all());

        if ($request->has('file')) {
            foreach ($request->file as $file) {
                $assets = $this->api->attach(['file' => $file])->post('api/assets');
                $MyWeek->assets()->save($assets);
            }
        } else if ($request->has('url')) {
            $assets = $this->api->post('api/assets', ['url' => $request->url]);
            $MyWeek->assets()->save($assets);
        } else if ($request->has('uuid')) {
            $a = Asset::byUuid($request->uuid)->get();
            $assets = Asset::findOrFail($a[0]->id);
            $MyWeek->assets()->save($assets);
        }

        return $this->response->created(url('api/MyWeek/' . $MyWeek->id));
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Entities\v1\MyWeek  $MyWeek
     * @return \Illuminate\Http\Response
     */
    public function show(MyWeek $MyWeek)
    {
        return $this->response->item($MyWeek, new MyWeekTransformer());
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\v1\MyWeek  $MyWeek
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MyWeek $MyWeek)
    {
        $request['updated_by'] = $request->user()->id;

        $rules = [
            'player_id' => 'required|integer|exists:users,id',
            'status_id' => 'required|integer|exists:conf_statuses,id',
            'file' => 'array',
            'file.*' => 'image|mimes:jpeg,jpg,png|max:2048',
        ];

        if ($request->method() == 'PATCH') {
            $rules = [
                'player_id' => 'sometimes|required|integer|exists:users,id',
                'status_id' => 'sometimes|required|integer|exists:conf_statuses,id',
                'file' => 'sometimes|required|array',
                'file.*' => 'image|mimes:jpeg,jpg,png|max:2048',

            ];
        }
        $this->validate($request, $rules);
        $MyWeek->update($request->except('created_by'));

        if ($request->has('file')) {
            foreach ($request->file as $file) {
                $assets = $this->api->attach(['file' => $file])->post('api/assets');
                $MyWeek->assets()->save($assets);
            }
        } else if ($request->has('url')) {
            $assets = $this->api->post('api/assets', ['url' => $request->url]);
            $MyWeek->assets()->save($assets);
        } else if ($request->has('uuid')) {
            $a = Asset::byUuid($request->uuid)->get();
            $assets = Asset::findOrFail($a[0]->id);
            $MyWeek->assets()->save($assets);
        }

        return $this->response->item($MyWeek->fresh(), new MyWeekTransformer());
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Entities\v1\MyWeek  $MyWeek
     * @return \Illuminate\Http\Response
     */
    public function destroy(MyWeek $MyWeek)
    {
        //
        $record = $this->model->findOrFail($MyWeek->id);
        $record->delete();
        return $this->response->noContent();
    }

}
