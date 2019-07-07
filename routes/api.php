<?php

use Illuminate\Http\Request;
// use App\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources(["users" => "API\UsersController"]);
Route::get("profile", "API\UsersController@profile");
Route::put("profile", "API\UsersController@updateProfile");

Route::get("findUser", "API\UsersController@search");
Route::get("loadCarBrands", "API\Brands\CarBrandsController@index");
Route::get("loadModels/{id}", "API\CarModels\CarModelsController@show");
Route::post("arrivals", "API\Arrivals\ArrivalsController@store");
Route::get("arrivals", "API\Arrivals\ArrivalsController@index");
Route::post("arrivals/services", "API\Arrivals\ArrivalServiceController@arrival_services_store");

Route::get("/arrivals/services/{id}", "API\Arrivals\ArrivalServiceController@show");

Route::get("services", "API\Services\CarServicesController@index");
Route::post("services", "API\Services\CarServicesController@store");
Route::put("services/{id}", "API\Services\CarServicesController@update");
Route::delete("services/{id}", "API\Services\CarServicesController@destroy");


// Route::post("users/getAll","API\UsersController@postUsers");
// Route::post("users/getAll",function(){
//     return User::latest()->paginate(10);
// });

