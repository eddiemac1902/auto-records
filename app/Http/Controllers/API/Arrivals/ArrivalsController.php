<?php

namespace App\Http\Controllers\API\Arrivals;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Arrivals\Arrivals;

class ArrivalsController extends Controller
{

    public function index()
    {
        $arrivals = Arrivals::select("arrivals.id", "arrivals.car_model_id", "car_number", "manufac_year", "customer_id", "car_models.model_name", "car_brands.name as brand_name", "arrivals.in_date", "customers.name as customer_name", "arrivals.created_at")
            ->leftJoin("car_models", "car_models.id", "=", "arrivals.car_model_id")
            ->leftJoin("car_brands", "car_models.car_brand_id", "=", "car_brands.id")
            ->leftJoin("customers", "customers.id", "=", "arrivals.customer_id")
            ->paginate(10);

        return $arrivals;
    }

    public function store(Request $request)
    {
        // $timestamp = new DateTime($request->in_date);
        // $request["in_date"] = $timestamp->format('Y-m-d H:i:s');
        // $request["in_date"] = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now());
        $request["in_date"] = \Carbon\Carbon::parse($request->in_date, "UTC")->toDateTimeString();
        // $request["in_date"] = new \DateTime($request->in_date);
        // gmdate('Y-m-d\TH:i:s\Z');

        // return $request->all();

        $validateData = $request->validate([
            "customer.id" => "required",
            "selected_model.id" => "required",
            "selected_brand.id" => "required",
            "manuf_year" => "required",
            "in_date" => "required",
            "mileage" => "required",
            "number_plate" => "required",
            "in_date" => "required"

        ]);

        $arrival = new Arrivals();
        $arrival->customer_id = $request->customer["id"];
        $arrival->car_number = $request->number_plate;
        $arrival->manufac_year = $request->manuf_year;
        $arrival->in_date = $request->in_date;
        $arrival->car_model_id = $request->selected_model["id"];
        $arrival->created_by = auth("api")->user()->id;
        $arrival->save();


    }

}
