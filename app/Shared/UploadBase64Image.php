<?php

namespace App\Shared;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadBase64Image
{
    /**
     * @param $base64_image
     * @param $image_path
     * @return string
     */
    public static function upload_image($base64_image,$image_path): string
    {
        $image_64 = $base64_image; //your base64 encoded data
        $extension = explode(';base64',$image_64);
        $extension = explode('/',$extension[0]);
        $extension = $extension[1];

        $replace = substr($image_64, 0, strpos($image_64, ',')+1);

        // find substring from replace here eg: data:image/png;base64,

        $image = str_replace($replace, '', $image_64);

        $image = str_replace(' ', '+', $image);

        $imageName = time().'_'.Str::random(20).'.'.$extension;

        Storage::disk('public')->put($image_path.'/' .$imageName, base64_decode($image));
        return $image_path.'/'.$imageName;
    }
}
