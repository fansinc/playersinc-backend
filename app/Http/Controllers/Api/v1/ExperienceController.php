<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\v1\Experience;
use App\Http\Controllers\Controller;
use App\Transformers\V1\ExperienceTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Entities\Assets\Asset;

class ExperienceController extends Controller
{
    use Helpers;
    protected $model;

    public function __construct(Experience $model)
    {
        $this->model = $model;
        // $this->middleware('permission:List experience')->only('index');
        // $this->middleware('permission:List experience')->only('show');
        $this->middleware('permission:Create experience')->only('store');
        $this->middleware('permission:Update experience')->only('update');
        $this->middleware('permission:Delete experience')->only('destroy');
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
        return $this->response->paginator($paginator, new ExperienceTransformer());
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
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            // 'status_id' => 'required|integer|exists:conf_statuses,id',
            'file' => 'array',
            'file.*' => 'image|mimes:jpeg,jpg,png|max:2048',
          
        ];
        $this->validate($request, $rules);
        $Experience = $this->model->create($request->all());

        if ($request->has('file')) {
            foreach ($request->file as $file) {
                $assets = $this->api->attach(['file' => $file])->post('api/assets');
                $Experience->assets()->save($assets);
            }
        } else if ($request->has('url')) {
            $assets = $this->api->post('api/assets', ['url' => $request->url]);
            $Experience->assets()->save($assets);
        } else if ($request->has('uuid')) {
            $a = Asset::byUuid($request->uuid)->get();
            $assets = Asset::findOrFail($a[0]->id);
            $Experience->assets()->save($assets);
        }

        return $this->response->created(url('api/Experience/' . $Experience->id));
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Entities\v1\Experience  $Experience
     * @return \Illuminate\Http\Response
     */
    public function show(Experience $Experience)
    {
        return $this->response->item($Experience, new ExperienceTransformer());
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\v1\Experience  $Experience
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Experience $Experience)
    {
        $request['updated_by'] = $request->user()->id;

        $rules = [
            'player_id' => 'required|integer|exists:users,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'status_id' => 'required|integer|exists:conf_statuses,id',
            'file' => 'array',
            'file.*' => 'image|mimes:jpeg,jpg,png|max:2048',
        ];

        if ($request->method() == 'PATCH') {
            $rules = [
                'player_id' => 'sometimes|required|integer|exists:users,id',
                'title' => 'sometimes|required|string',
                'description' => 'sometimes|required|string',
                'price' => 'sometimes|required|regex:/^\d+(\.\d{1,2})?$/',
                'status_id' => 'sometimes|required|integer|exists:conf_statuses,id',
                'file' => 'sometimes|required|array',
                'file.*' => 'image|mimes:jpeg,jpg,png|max:2048',

            ];
        }
        $this->validate($request, $rules);
        $Experience->update($request->except('created_by'));

        if ($request->has('file')) {
            foreach ($request->file as $file) {
                $assets = $this->api->attach(['file' => $file])->post('api/assets');
                $Experience->assets()->save($assets);
            }
        } else if ($request->has('url')) {
            $assets = $this->api->post('api/assets', ['url' => $request->url]);
            $Experience->assets()->save($assets);
        } else if ($request->has('uuid')) {
            $a = Asset::byUuid($request->uuid)->get();
            $assets = Asset::findOrFail($a[0]->id);
            $Experience->assets()->save($assets);
        }

        return $this->response->item($Experience->fresh(), new ExperienceTransformer());
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Entities\v1\Experience  $Experience
     * @return \Illuminate\Http\Response
     */
    public function destroy(Experience $Experience)
    {
        //
        $record = $this->model->findOrFail($Experience->id);
        $record->delete();
        return $this->response->noContent();
    }
}
