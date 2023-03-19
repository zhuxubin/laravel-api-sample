<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlbumRequest;
use App\Http\Resources\AlbumResource;
use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index(Request $request)
    {
        return AlbumResource::collection(Album::where('user_id', $request->user()->id)->paginate());
    }

    public function store(AlbumRequest $request)
    {
        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        return new AlbumResource(Album::create($data));
    }

    public function show(Request $request, Album $album)
    {
        if ($album->user_id != $request->user()->id) {
            return abort(403, '未经授权操作');
        }
        return new AlbumResource($album);
    }

    public function update(AlbumRequest $request, Album $album)
    {
        if ($album->user_id != $request->user()->id) {
            return abort(403, '未经授权操作');
        }
        $album->update($request->all());
        return new AlbumResource($album);
    }

    public function destroy(Request $request, Album $album)
    {
        if ($album->user_id != $request->user()->id) {
            return abort(403, '未经授权操作');
        }
        $album->delete();
        return response('', 204);
    }
}
