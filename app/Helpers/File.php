<?php

namespace App\Helpers;

use App\Helpers\Enum\Path;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class File
{
    public static function uploadImage(UploadedFile $imageFile, string $customPath): string
    {
        $imageName = time().'.'.$imageFile->extension();
        $imageFile->storeAs($customPath, $imageName);

        return $imageName;
    }

    public static function deleteFile(string $path, string $filename): void
    {
        Storage::delete(self::getFileStoragePath($path, $filename));
    }

    public static function getFilePublicPath(string $path, string $filename = null): string
    {
        return (!is_null($filename))
            ? public_path(Path::STORAGE->value.$path.$filename)
            : public_path(Path::STORAGE->value.$path);
    }

    public static function getFileStoragePath(string $path, string $filename = null): string
    {
        return (!is_null($filename))
            ? Path::STORAGE_PUBLIC->value.$path.$filename
            : Path::STORAGE_PUBLIC->value.$path;
    }

    public static function getExposedPath(string $path, string $filename = null): string
    {
        return (!is_null($filename))
            ? Path::STORAGE->value.$path.$filename
            : Path::STORAGE->value.$path;
    }
}
