<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class ResizeImageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'image' => ['required'],
            'w' => ['required', 'regex:/^\d+(\.\d+)?%?$/'],
            'h' => 'regex:/^\d+(\.\d+)?%?$/',
            'album_id' => 'exists:\App\Models\Album,id'
        ];

        $all = $this->all();
        // 判断图片上传操作的类型：1、图片句柄 2、图片远程url
        if (isset($all['image']) && $all['image'] instanceof UploadedFile) {
            $rules['image'][] = 'image';
        } else {
            $rules['image'][] = 'url';
        }

        return $rules;
    }
}
