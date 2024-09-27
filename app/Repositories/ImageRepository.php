<?php

namespace App\Repositories;

use App\Models\DefaultImage;
use App\Models\Image;
use App\Repositories\Products\BaseRepository;
use App\Services\ErrorLogger;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class ImageRepository extends BaseRepository
{
    private $storage;
    public function __construct(Image $vendor)
    {
        parent::__construct($vendor);
        $this->storage = config('filesystems.default');
    }


    public function moveImages($request, $model, $image, $is_default = 0)
    {
        try {
            $image_path = $image->store('uploads', $this->storage);
            return $image_path;
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in moveImages method in ImageRepository");
        }
    }



    public function insertImages($request, $model, $image_path, $is_default = 0)
    {
        try {
            $image_created = $model->images()->create([
                'title' => basename($image_path),
                'path' => Storage::url($image_path),
                'is_default' =>  $is_default
            ]);
            if (!$image_created) {
                throw new Exception("Image not created");
            }
            return $image_created;
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in insertImages method in ImageRepository");
        }
    }



    public function storeImages($request, $model)
    {
        try {
            if (count($request->image) > 0) {
                foreach ($request->image as $image) {
                    $image_path = $this->moveImages($request, $model, $image);
                    $this->insertImages($request, $model,  $image_path, 0);
                }
            }
            if ($request->has('default_image')) {
                $image_path_default = $this->moveImages($request, $model, $request->default_image);
                $default_img = $this->insertImages($request, $model,  $image_path_default, is_default: 1);
                $this->saveMultiResolutionImages($default_img, $image_path_default);
            }
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in storeImages method in ImageRepository");
        }
    }

    public function moveMultiResolutionImages($default_img, $manager, $image_size)
    {

        $imagePath = Storage::path('uploads/' . $default_img->title);
        $image = $manager->read(input: $imagePath);
        $array_dimensions = config('image.options.size');
        if (isset($array_dimensions[$image_size])) {
            $image->resize($array_dimensions[$image_size][0], $array_dimensions[$image_size][1]);
        }
        $directory = 'uploads/' . $default_img->id . '/';
        Storage::makeDirectory($directory);
        $image_saved_path = $directory . $image_size . "_" . $default_img->title;
        $image->save(Storage::path($image_saved_path));
        return $image_saved_path;
    }

    public function saveMultiResolutionImages($default_img, $image_path_default)
    {
        try {
            $manager = new ImageManager(new Driver());
            $sizes = ["small", "medium", "large"];

            foreach ($sizes as $size) {
                $saved_image_path = $this->moveMultiResolutionImages($default_img, $manager, $size);

                DefaultImage::create([
                    'title' => $size . '_' . basename($image_path_default),
                    'path' => Storage::url($saved_image_path),  // Use the local storage URL for saved image
                    'image_id' =>  $default_img->id,
                    'image_size' => $size
                ]);
            }
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in storeImages method in ImageRepository");
        }
    }

    public function editDefaultImage() {}
}
