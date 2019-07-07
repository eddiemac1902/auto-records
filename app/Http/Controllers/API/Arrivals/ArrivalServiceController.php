<?php

namespace App\Http\Controllers\API\Arrivals;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Arrivals\ArrivalService;

class ArrivalServiceController extends Controller
{
    public function index()
    {

    }

    public function arrival_services_store(Request $request)
    {

        $messages = [
            "selected_arrival_service.id.required" => "The service is required"
        ];
        $validateData = $request->validate([
            "selected_arrival_service.id" => "required"
        ], $messages);

        // return $request->all();

        $arrival_service = new ArrivalService();
        $arrival_service->arrival_id = $request->arrival_id;
        $arrival_service->service_id = $request->selected_arrival_service["id"];
        $arrival_service->save();
        return $arrival_service;


    }

    public function show($id)
    {
        $arrival_services = ArrivalService::where("arrival_id", $id)
            ->select("arrival_services.id", "services.name as service_name", "services.price as service_price", "arrival_services.created_at")
            ->leftJoin("services", "services.id", "=", "arrival_services.service_id")
            ->get();

        return $arrival_services;
    }
}
