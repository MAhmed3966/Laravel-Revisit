<?php

namespace App\Http\Controllers;

use App\Repositories\ImageSettingRepository;
use Illuminate\Http\Request;

class ImageSettingsController extends BaseController
{
    public $image_settings_repo;

    public function __construct(ImageSettingRepository $image_settings_repo)
    {
        $this->image_settings_repo = $image_settings_repo;
    }
    public function update($id, Request $request)
    {
        try {
            $data = [
                "size" => $request->size,
                "dimension" => [
                    "width" => $request->width,
                    "height" => $request->height
                ]
            ];
            $result = $this->image_settings_repo->update($id, $data);
            if ($result) {
                return $this->successResponse(null, "Image settings updated");
            } else {
                return $this->errorResponse("Image settings Not updated", "", 500);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), "", 500);
        }
    }
}
