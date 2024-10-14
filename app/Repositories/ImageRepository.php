<?php

namespace App\Repositories;

use App\Models\DefaultImage;
use App\Models\Image;
use App\Models\ImageSettings;
use App\Repositories\Products\BaseRepository;
use App\Services\ErrorLogger;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\ValidationException;

class ImageRepository extends BaseRepository
{
    private $storage;

    protected $model;
    public $modelInstanceOf;

    public $path;
    public function __construct(Image $vendor)
    {
        parent::__construct($vendor);
        $this->storage = config('filesystems.default');
    }

    public function getModelToString($model)
    {
        return class_basename(get_class($model));
    }

    public function storeImages($request, $model)
    {
        try {
            $this->model = $model;
            $this->modelInstanceOf = $this->getModelToString($this->model);
            $this->path = $this->modelInstanceOf.'/'.$this->model->id.'/uploads/';
            if (!$request->has('existing_multiple_image_ids') && count($request->image) > 0) {
                foreach ($request->image as $image) {
                    $image_path = $this->moveImages($request, $image);
                    $this->insertImages($request, $image_path, 0);
                }
            }
            if ($request->has('default_image')) {
                $validator = Validator::make($request->all(), [
                    'default_image' => 'mimes:jpeg,png,jpg',
                ]);
                if ($validator->fails()) {
                    throw ValidationException::withMessages($validator->errors()->toArray());
                }
                $image_path_default = $this->moveImages($request,  $request->default_image);
                $default_img = $this->insertImages($request,  $image_path_default, is_default: 1);
                $this->saveMultiResolutionImages($request, $default_img, $image_path_default);
            }
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in storeImages method in ImageRepository");
        }
    }


    public function moveImages($request, $image, $is_default = 0)
    {
        try {
            $image_path = $image->store($this->path, $this->storage);
            if (!$image_path) {
                throw new FileNotFoundException('The file could not be stored.');
            }
            return $image_path;
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in moveImages method in ImageRepository");
        }
    }



    public function insertImages($request, $image_path, $is_default = 0)
    {
        try {
            $image_created = $this->model->images()->create([
                'title' => basename($image_path),
                'path' => Storage::url($image_path),
                'is_default' =>  $is_default
            ]);
            return $image_created;
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in insertImages method in ImageRepository");
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
            ErrorLogger::logAndThrow($e, "Error is in saveMultiResolutionImages method in ImageRepository");
        }
    }




    public function moveMultiResolutionImages($request, $default_img, $manager, $image_attribtes)
    {
        try {
            $image_size = $image_attribtes['size'];
            $imagePath = Storage::path(path:$this->path . $default_img->title);

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
            return $this->compressAndSave( $imagePath, $image_attribtes, $image, $default_img, $image_size);
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in moveMultiResolutionImages method in ImageRepository");
        }
    }

    public function compressAndSave($imagePath, $image_attribtes, $image, $default_img, $image_size)
    {
        try {
            list($width, $height) = getimagesize($imagePath);
            $array_dimensions = json_decode($image_attribtes['dimension']);
            if (isset($array_dimensions)) {
                $thumb = imagecreatetruecolor($array_dimensions->width, $array_dimensions->height);
                imagecopyresized($thumb, $image, 0, 0, 0, 0, $array_dimensions->width, $array_dimensions->height, $width, $height);
            }
            $directory = $this->path. $default_img->id . '/';
            $image_saved_path = $directory . $image_size . "_" . $default_img->title;
            //Ensure the directory exists
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory, 755, true);
            }
            //Get the full path where the image will be saved
            $fullImagePath = Storage::path(path: $image_saved_path);
            imagejpeg($thumb, $fullImagePath, quality: 75); // Try quality = 75 for better compression
            // Clean up
            imagedestroy($image);
            return $image_saved_path;
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in compressAndSave method in ImageRepository");
        }
    }




    public function editDefaultImage() {}
}
