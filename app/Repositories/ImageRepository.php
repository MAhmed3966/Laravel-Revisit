<?php

namespace App\Repositories;

use App\Models\Image;
use App\Models\Product;
use App\Models\Vendor;
use App\Repositories\Products\BaseRepository;
use App\Services\ErrorLogger;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;


class ImageRepository extends BaseRepository
{
    private $storage;
    public function __construct(Image $vendor)
    {
        parent::__construct($vendor);
        $this->storage = config('filesystems.default');
    }


    public function moveImages($model, $image, $is_default = 0)
    {
        try {
            $image_path = $image->store('uploads', $this->storage);
            // $image->move(storage_path('app/public'), $image_name);
            $this->insertImages($model,  $image_path, $is_default, $image);
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in moveImages method in ImageRepository");
        }
    }

    public function saveMultiResolutionImages($image)
    {
        // dd($image->getRealPath());
        try {
            $manager = new ImageManager(
                new Driver()
            );
            $destinattion_path = config('image.options.image_path');
            $img = $manager->make($image->getRealPath());

            $largeImage = $img->resize(1024, 768, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $largeImage->save($destinattion_path . 'image_large.jpg');

            // Create and save medium image
            $mediumImage = $img->resize(640, 480, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $mediumImage->save($destinattion_path . 'image_medium.jpg');

            // Create and save small image
            $smallImage = $img->resize(320, 240, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $smallImage->save($destinattion_path . 'image_small.jpg');
        } catch (\Exception $e) {
            dd($e->getMessage(),"here");
        }

        // $image_manipulation =  $manager->read($image_path);
    }
    public function insertImages($model, $image_path, $is_default, $image)
    {
        try {
            $image_created = $model->images()->create([
                'title' => $image_path,
                'path' => config('image.options.image_path') . $image_path,
                'is_default' =>  $is_default
            ]);
            if ($is_default = 1) {
                $this->saveMultiResolutionImages($image);
            }
            if (!$image_created) {
                throw new Exception("Image not created");
            }
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in insertImages method in ImageRepository");
        }
    }



    public function storeImages($request, $model)
    {
        try {
            if (count($request->image) > 0) {
                foreach ($request->image as $image) {
                    $this->moveImages($model, $image);
                }
                // dd($model, $model->images()->get());
                if ($request->has('default_image')) {
                    // $manager = new ImageManager('gd');//

                    $default_image_name = $this->moveImages($model, $request->default_image, 1);
                    // $default_image_arr = [];
                    // $default_image_arr[0] = $manager->make($image)->resize(150, 150)->encode($image->extension());
                    // $default_image_arr[1] = $manager->make($image)->resize(150, 150)->encode($image->extension());
                    // $default_image_arr[2] = $manager->make($image)->resize(150, 150)->encode($image->extension());

                }
            }
        } catch (\Exception $e) {
            ErrorLogger::logAndThrow($e, "Error is in storeImages method in ImageRepository");
        }
    }
}
