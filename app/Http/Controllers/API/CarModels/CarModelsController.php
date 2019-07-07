<?php

namespace App\Http\Controllers\API\CarModels;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Cars\CarModel;

class CarModelsController extends Controller
{
    public function show($id)
    {
        $models = CarModel::select("id", "model_name as label")->where("car_brand_id", $id)->get();

        return $models;
    }
}
