<?php


namespace App\Repositories;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaRepository extends Repository
{
    public function model()
    {
        return Media::class;
    }

    public function storeByRequest(UploadedFile $file, string $path, string $description = null, string $type = null): Media
    {

        $path = $file->store(trim($path, '/'), 'public');
        $extension = $file->extension();
        if(!$type){
            $type = in_array($extension, ['jpg', 'png', 'jpeg', 'gif', 'webp']) ? 'Image' : $extension;
        }

        return $this->create([
            'type' => $type,
            'name' => basename(str_replace('\\', '/', $file->getClientOriginalName())),
            'src' =>  $path,
            'extension' => $extension,
            'path' => $path,
            'description' => $description,
        ]);
    }

    public function updateByRequest(UploadedFile $file,string $path, string $type = null, Media $media): Media
    {

        $path = $file->store(trim($path, '/'), 'public');
        $extension = $file->extension();
        if(!$type){
            $type = in_array($extension, ['jpg', 'png', 'jpeg', 'gif', 'webp']) ? 'image' : $extension;
        }

        if (Storage::disk('public')->exists($media->src)) {
            Storage::disk('public')->delete($media->src);
        }

        $media->update([
            'type' => $type,
            'name' => basename(str_replace('\\', '/', $file->getClientOriginalName())),
            'src' =>  $path,
            'extension' => $extension,
            'path' => $path,
        ]);
        return $media;
    }

    public function updateOrCreateByRequest(UploadedFile $file, string $path, string $type = 'Image', $media = null): Media
    {
        $src = $file->store(trim($path, '/'), 'public');
        $extension = $file->extension();
        if ($media && Storage::disk('public')->exists($media->src)) {
            Storage::disk('public')->delete($media->src);
        }

        if (!$type) {
            $type = in_array($extension, ['jpg', 'png', 'jpeg', 'gif', 'webp']) ? 'Image' : $extension;
        }
        return $this->query()->updateOrCreate([
            'id' => $media?->id ?? 0,
        ],[
            'type' => $type,
            'src' => $src,
            'extension' => $extension,
            'path' => $path,
        ]);
    }
}
