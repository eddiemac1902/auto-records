<?php

namespace App\Http\Controllers\API\Brands;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Cars\Brand;
use App\Model\Customers\Customer;
use App\Model\Services\CarService;

class CarBrandsController extends Controller
{
    public function index()
    {
        // $brands = Brand::all();
        $brands = Brand::select("id", "name as label")->get();
        $years = [];
        // for (; $i <= date("Y"); $i++) {
        //     $years[] = $i;
        // }

        for ($i = date("Y"); $i >= 1985; $i--) {
            $years[] = $i;
        }

        $customers = Customer::select("id", "name as label")->get();

        $car_services = CarService::where("deleted", 0)->select("id", "name as label")->get();
        return response()->json(["brands" => $brands, "manuf_years" => $years, "customers" => $customers, "car_services" => $car_services]);
    }
}
