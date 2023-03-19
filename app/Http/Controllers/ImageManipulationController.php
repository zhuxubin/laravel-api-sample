<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResizeImageRequest;
use App\Http\Resources\ImageManipulationResource;
use App\Models\Album;
use App\Models\ImageManipulation;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageManipulationController extends Controller
{
    public function index(Request $request)
    {
        return ImageManipulationResource::collection(ImageManipulation::where('user_id', $request->user()->id)->paginate());
    }

    public function show(Request $request, ImageManipulation $image)
    {
        if ($image->user_id != $request->user()->id) {
            return abort(403, '未经授权操作');
        }
        return new ImageManipulationResource($image);
    }

    public function getByAlbum(Request $request, Album $album)
    {
        if ($album->user_id != $request->user()->id) {
            return abort(403, '未经授权操作');
        }

        return ImageManipulationResource::collection(ImageManipulation::where([
            'user_id' => $request->user()->id,
            'album_id' => $album->id
        ])->paginate());
    }

    public function store(ResizeImageRequest $request)
    {
        $all = $request->all();

        $image = $all['image'];
        unset($all['image']);

        $data = [
            'type' => ImageManipulation::TYPE_RESIZE,
            'data' => json_encode($all),
            'user_id' => $request->user()->id
        ];

        // 是否存在专辑ID
        if (isset($all['album_id'])) {
            $album = Album::find($all['album_id']);
            if ($album->user_id != $request->user()->id) {
                return abort(403, '未经授权操作');
            }
            $data['album_id'] = $all['album_id'];
        }

        $dir = 'images/' . Str::random() . '/';
        $absolutePath = public_path($dir);
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        if ($image instanceof UploadedFile) {
            $data['name'] = $image->getClientOriginalName();
            $filename = pathinfo($data['name'], PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $originalPath = $absolutePath . $data['name'];
            $data['path'] = $dir . $data['name'];
            $image->move($absolutePath, $data['name']);
        } else {
            $data['name'] = pathinfo($image, PATHINFO_BASENAME);
            $filename = pathinfo($image, PATHINFO_FILENAME);
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            $originalPath = $absolutePath . $data['name'];
            copy($image, $originalPath);
            $data['path'] = $dir . $data['name'];
        }

        $w = $all['w'];
        $h = $all['h'] ?? false;
        list($image, $width, $height) = $this->getWidthAndHeight($w, $h, $originalPath);

        $resizedFilename = $filename . time() . $extension;
        $image->resize($width, $height)->save($absolutePath . $resizedFilename);

        $data['output_path'] = $dir . $resizedFilename;

        $imageManipulation = ImageManipulation::create($data);

        return new ImageManipulationResource($imageManipulation);
    }

    public function destroy(Request $request, ImageManipulation $image)
    {
        if ($image->user_id != $request->user()->id) {
            return abort(403, '未经授权操作');
        }
        $image->delete();
        return response('', 204);
    }

    protected function getWidthAndHeight($w, $h, $originalPath)
    {
        $image = Image::make($originalPath);
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        if (str_ends_with($w, '%')) { // 按比例缩放图片
            $ratioW = (float)(str_replace('%', '', $w));
            $ratioH = $h ? (float)(str_replace('%', '', $h)) : $ratioW;
            $newWidth = $originalWidth * $ratioW / 100;
            $newHeight = $originalHeight * $ratioH / 100;
        } else { // 指定宽、高缩放图片
            $newWidth = (float)$w;
            $newHeight = $h ? (float)$h : ($originalHeight * $newWidth / $originalWidth);
        }

        return [$image, $newWidth, $newHeight];
    }
}
