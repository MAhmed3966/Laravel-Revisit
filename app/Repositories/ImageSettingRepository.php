<?php
namespace App\Repositories;

use App\Models\ImageSettings;
use App\Repositories\Products\BaseRepository;

class ImageSettingRepository extends BaseRepository
{
    public function __construct(ImageSettings $image_settings){
        parent::__construct($image_settings);
    }
}
