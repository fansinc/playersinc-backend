<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\v1\News;
use App\Http\Controllers\Controller;
use App\Transformers\V1\NewsTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Entities\Assets\Asset;
use Dingo\Api\Exception\StoreResourceFailedException;

class NewsController extends Controller
{
    use Helpers;
    protected $model;

    public function __construct(News $model)
    {
        $this->model = $model;
        // $this->middleware('permission:List news')->only('index');
        // $this->middleware('permission:List news')->only('show');
        $this->middleware('permission:Create news')->only('store');
        $this->middleware('permission:Update news')->only('update');
        $this->middleware('permission:Delete news')->only('destroy');
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
        return $this->response->paginator($paginator, new NewsTransformer());
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

        if ($request->type == "image" || $request->type == "video") {

            
        }
        else
        {

            throw new StoreResourceFailedException('Error! File type is not valid!');
        }

        if($request->type=="image")
        {
            $rules = [
                'player_id' => 'required|integer|exists:users,id',
                'title' => 'required|string',
                'description' => 'required|string',
                // 'status_id' => 'required|integer|exists:conf_statuses,id',
                'type' => 'required|string|in:image,video',
                'file' => 'array',
                'file.*' => 'image|mimes:jpeg,jpg,png|max:3072',//3mb
            
            ];
        }
        else if($request->type=="video")
        {
            $rules = [
                'player_id' => 'required|integer|exists:users,id',
                'title' => 'required|string',
                'description' => 'required|string',
                // 'status_id' => 'required|integer|exists:conf_statuses,id',
                'type' => 'required|string|in:image,video',
                'file' => 'array',
                'file.*' => 'mimes:mp4|max:15360',//15mb
            
            ];

        }

        $this->validate($request, $rules);
        $News = $this->model->create($request->all());

        if ($request->has('file')) {
            foreach ($request->file as $file) {
                $assets = $this->api->attach(['file' => $file])->post('api/assets');
                $News->assets()->save($assets);
            }
        } else if ($request->has('url')) {
            $assets = $this->api->post('api/assets', ['url' => $request->url]);
            $News->assets()->save($assets);
        } else if ($request->has('uuid')) {
            $a = Asset::byUuid($request->uuid)->get();
            $assets = Asset::findOrFail($a[0]->id);
            $News->assets()->save($assets);
        }

        return $this->response->created(url('api/News/' . $News->id));
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Entities\v1\News  $News
     * @return \Illuminate\Http\Response
     */
    public function show(News $News)
    {
        return $this->response->item($News, new NewsTransformer());
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\v1\News  $News
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, News $News)
    {
        $request['updated_by'] = $request->user()->id;

        if ($request->has('file') && $request->method() == 'PUT') {

            if ($request->type=="image" || $request->type=="video") {

                
            }
            else
            {
                throw new StoreResourceFailedException('Error! File type is not valid!');

            }

        }
        

        if($request->type=="image")
        {
            $rules = [
                'player_id' => 'required|integer|exists:users,id',
                'title' => 'required|string',
                'description' => 'required|string',
                'status_id' => 'required|integer|exists:conf_statuses,id',
                'type' => 'required|string|in:image,video',
                'file' => 'array',
                'file.*' => 'image|mimes:jpeg,jpg,|max:3072',//3mb
            ];
        }
        else if($request->type=="video")
        {

            $rules = [
                'player_id' => 'required|integer|exists:users,id',
                'title' => 'required|string',
                'description' => 'required|string',
                'status_id' => 'required|integer|exists:conf_statuses,id',
                'type' => 'required|string|in:image,video',
                'file' => 'array',
                'file.*' => 'mimes:mp4|max:15360',//15mb
            ];
        }

        if ($request->method() == 'PATCH') {

            if($request->has('file') && $request->type=="image")
            {
                $rules = [
                    'player_id' => 'sometimes|required|integer|exists:users,id',
                    'title' => 'sometimes|required|string',
                    'description' => 'sometimes|required|string',
                    'status_id' => 'sometimes|required|integer|exists:conf_statuses,id',
                    'type' => 'sometimes|required|string|in:image,video',
                    'file' => 'sometimes|required|array',
                    'file.*' => 'image|mimes:jpeg,jpg,png|max:3072',//3mb

                ];
            } 
            
            else if($request->has('file') && $request->type=="video")
            {  

                $rules = [
                    'player_id' => 'sometimes|required|integer|exists:users,id',
                    'title' => 'sometimes|required|string',
                    'description' => 'sometimes|required|string',
                    'status_id' => 'sometimes|required|integer|exists:conf_statuses,id',
                    'type' => 'sometimes|required|string|in:image,video',
                    'file' => 'array',
                    'file.*' => 'mimes:mp4|max:15360',//15mb

                ];
            }  

            else
            {  

                $rules = [
                    'player_id' => 'sometimes|required|integer|exists:users,id',
                    'title' => 'sometimes|required|string',
                    'description' => 'sometimes|required|string',
                    'status_id' => 'sometimes|required|integer|exists:conf_statuses,id',
                    'type' => 'sometimes|required|string|in:image,video',
                    'file' => 'sometimes|required|array',
                  // 'file.*' => 'image|mimes:jpeg,jpg,png,mp4|max:3072',//3mb
                  'file.*' => 'mimes:jpeg,jpg,png,mp4|max:3072',//3mb

                ];
            }  
        }
        $this->validate($request, $rules);

        
        // return $News->assets;
        if($request->has('file') || $request->has('url') || $request->has('uuid')) {

         
             $News->assets()->delete();
        }
        
        $News->update($request->except('created_by'));

        if ($request->has('file')) {
            foreach ($request->file as $file) {
                $assets = $this->api->attach(['file' => $file])->post('api/assets');
                $News->assets()->save($assets);
            }
        } else if ($request->has('url')) {
            $assets = $this->api->post('api/assets', ['url' => $request->url]);
            $News->assets()->save($assets);
        } else if ($request->has('uuid')) {
            $a = Asset::byUuid($request->uuid)->get();
            $assets = Asset::findOrFail($a[0]->id);
            $News->assets()->save($assets);
        }

        return $this->response->item($News->fresh(), new NewsTransformer());
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Entities\v1\News  $News
     * @return \Illuminate\Http\Response
     */
    public function destroy(News $News)
    {
        //
        $record = $this->model->findOrFail($News->id);
        $record->delete();
        return $this->response->noContent();
    }
}
