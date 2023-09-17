<?php

namespace App\Traits;

use DomDocument;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use Exception;
use Carbon\Carbon;
use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

trait FileUploadTrait
{
    /**
     * Upload file to default storage disk
     *
     * @access public
     *
     * @param UploadedFile $file
     * @param string $type
     * @param string|null $folder
     * @param string|null $fileName
     * @param array $data
     * @return bool|File
     */
    public function upload(UploadedFile $file,
                           string       $type,
                           string       $folder = null,
                           string       $fileName = null,
                           array        $data = []): bool|File
    {
        $data["user_id"] = 1 /*auth()->id()*/;
        $data['type'] = $type;
        $data['duration'] = $data['duration'] ?? null;
        // Get extension
        $data['ext'] = $file->extension();
        // Get mime type
        $data['mime'] = $file->getMimeType();
        // Generate file name
        $fileName = $fileName ?
            $fileName . '_' . Carbon::now()->timestamp . Str::random(4) . '.' . $data['ext'] :
            Carbon::now()->timestamp . Str::random(4) . '-' . $file->getClientOriginalName();
        // Remove special characters
        $fileName = $this->filterFileName($fileName, true);
        $data['name'] = $fileName;
        // Get default folder
        if (empty($folder)) {
            [$folder] = explode('_', $type);
            $folder = config("file.upload.paths.{$folder}", config('file.upload.paths.default'));
        }
        // Create folder if not exists, or abort uploading
        if (!$this->createDirectoryIfNotExists($folder)) {
            return false;
        }
        // check if the file is image
        if (Str::contains($data['mime'], 'image')) {
            $image = $this->generateImage($file);
            // Get width & height
            $data['width'] = $image->width();
            $data['height'] = $image->height();
            $data['url'] = $folder . $fileName;
            // Save image to storage disk
            Storage::put($data['url'], $image->stream($data['ext'], config('file.upload.quality')));
        } else {
            // Save file to storage disk and get file URL
            $data['url'] = Storage::putFileAs(rtrim($folder, "/"), $file, $fileName);
        }
        // Save new file object to DB
        if (!empty($data["current_id"])) {
            $file_obj = File::find($data["current_id"]);
            if ($file_obj) {
                $file_obj->fill($data);
                $file_obj->update();
                return $file_obj;
            }
        }
        return File::create($data);
    }

    /**
     * Check and create directory if not exists.
     *
     * @access protected
     *
     * @param string $folder
     *
     * @return bool|Exception
     */
    public function createDirectoryIfNotExists(string $folder): bool|Exception
    {
        // Check if dri exists
        if (Storage::exists($folder)) {
            return true;
        }
        // Create new dir
        try {
            Storage::makeDirectory($folder);
            return true;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function generateImage($file): \Intervention\Image\Image
    {
        $maxWidth = config('file.upload.max_width');
        // Create Intervention image
        $image = Image::make($file);
        // resize image
        if ($image->width() > $maxWidth) {
            $image->resize($maxWidth, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        return $image;
    }

    public function filterFileName($filename, $beautify = false): array|string|null
    {
        $filteredName = preg_replace('/[^\x{0600}-\x{06FF}A-Za-z0-9.]/u', '-', $filename);
        // optional beautification
        if ($beautify) {
            $filteredName = $this->beautifyFilename($filteredName);
        }
        return $filteredName;
    }

    public function beautifyFilename($filename): string
    {
        // reduce consecutive characters
        // "file   name.zip" becomes "file-name.zip"
        // "file___name.zip" becomes "file-name.zip"
        // "file---name.zip" becomes "file-name.zip"
        $filename = preg_replace(array(
            '/ +/',
            '/_+/',
            '/-+/'
        ), '-', $filename);
        // "file--.--.-.--name.zip" becomes "file.name.zip"
        // "file...name..zip" becomes "file.name.zip"
        $filename = preg_replace(array(
            '/-*\.-*/',
            '/\.{2,}/'
        ), '.', $filename);
        // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
        // $filename = mb_strtolower($filename, mb_detect_encoding($filename));
        $filename = mb_strtolower($filename, 'UTF-8');
        // ".file-name.-" becomes "file-name"
        return trim($filename, '.-');
    }


}
