<?php

namespace App\Repositories;

use App\Models\DefaultImage;
use App\Models\Image;
use App\Models\ImageSettings;
use App\Repositories\Products\BaseRepository;
use App\Services\ErrorLogger;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\ValidationException;

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
            dd($e->getMessage());
            ErrorLogger::logAndThrow($e, "Error is in insertImages method in ImageRepository");
        }
    }



    public function storeImages($request, $model)
    {
            if (count($request->image) > 0) {
                foreach ($request->image as $image) {
                    $image_path = $this->moveImages($request, $model, $image);
                    $this->insertImages($request, $model,  $image_path, 0);
                }
            }
            if ($request->has('default_image')) {
                $validator = Validator::make($request->all(), [
                    'default_image' => 'mimes:jpeg,png,jpg',
                ]);
                if ($validator->fails()) {
                    throw ValidationException::withMessages($validator->errors()->toArray());

                }
                $image_path_default = $this->moveImages($request, $model, $request->default_image);
                $default_img = $this->insertImages($request, $model,  $image_path_default, is_default: 1);
                $this->saveMultiResolutionImages($request, $default_img, $image_path_default);
            }
        // } catch (\Exception $e) {
        //     dd($e->getMessage());
        //     ErrorLogger::logAndThrow($e, "Error is in storeImages method in ImageRepository");
        // }
    }

    public function moveMultiResolutionImages($request, $default_img, $manager, $image_attribtes)
    {
        try {
            $image_size = $image_attribtes['size'];
            $imagePath = Storage::path('uploads/' . $default_img->title);
            $extension = $request->default_image->getClientOriginalExtension();
            switch (strtolower($extension)) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($imagePath);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($imagePath);
                    break;
                default:
                    return response()->json(['error' => 'Unsupported image format'], 400);
            }
            list($width, $height) = getimagesize($imagePath);
            $array_dimensions = json_decode($image_attribtes['dimension']);
            if (isset($array_dimensions)) {
                $thumb = imagecreatetruecolor($array_dimensions->width, $array_dimensions->height);
                imagecopyresized($thumb, $image, 0, 0, 0, 0, $array_dimensions->width, $array_dimensions->height, $width, $height);
            }
            $directory = '/uploads/' . $default_img->id . '/';
            $image_saved_path = $directory . $image_size . "_" . $default_img->title;
            // // Ensure the directory exists
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory, 755, true);
            }
            // // Get the full path where the image will be saved
            $fullImagePath = Storage::path(path: $image_saved_path);
            // // dd($fullImagePath);        // Save the image
            imagejpeg($thumb, $fullImagePath, quality: 75); // Try quality = 75 for better compression
            // Clean up
            imagedestroy($image);
            return $image_saved_path;
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in moveMultiResolutionImages method in ImageRepository");
        }
    }

    public function saveMultiResolutionImages($request, $default_img, $image_path_default)
    {
        try {
            $manager = new ImageManager(new Driver());
            $dimensions = ImageSettings::all()->select('size', 'dimension')->toArray();
            foreach ($dimensions as $key => $value) {
                $saved_image_path = $this->moveMultiResolutionImages($request, $default_img, $manager, $value);
                DefaultImage::create([
                    'title' => $value['size'] . '_' . basename($image_path_default),
                    'path' => Storage::url($saved_image_path),  // Use the local storage URL for saved image
                    'image_id' =>  $default_img->id,
                    'image_size' => $value['size']
                ]);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            ErrorLogger::logAndThrow($e, "Error is in saveMultiResolutionImages method in ImageRepository");
        }
    }

    public function editDefaultImage() {}
}
