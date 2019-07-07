<?php

namespace App\Http\Controllers\API\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Services\CarService;

class CarServicesController extends Controller
{
    public function index()
    {
        $services = CarService::where("deleted", 0)->get();
        return $services;
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            "name" => "required|unique:services,name",
            "price" => "required|numeric"
        ]);

        $carService = new CarService();
        $carService->name = $request->name;
        $carService->price = $request->price;
        $carService->created_by = auth("api")->user()->id;
        $carService->save();

        return $carService;
    }

    public function update(Request $request, $id)
    {
        $service = CarService::findOrFail($id);

        $validateData = $request->validate([
            "name" => "required",
            "price" => "required|numeric"
        ]);

        $service = CarService::findOrFail($id);
        $service->name = $request->name;
        $service->price = $request->price;
        $service->updated_by = auth("api")->user()->id;
        $service->save();

        return ["message" => "updated"];
    }

    public function destroy($id)
    {
        $service = CarService::findOrFail($id);
        $service->deleted = 1;
        $service->deleted_by = auth("api")->user()->id;
        $service->deleted_time = now();
        $service->save();
        return "success";
    }
}
