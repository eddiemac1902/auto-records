<?php

use Illuminate\Database\Seeder;
use App\Model\Cars\Brand;
use App\Model\Cars\CarModel;


class CarBrandModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $url = "https://raw.githubusercontent.com/FormidableLabs/radon-typeahead/master/demo/car-models.json";
        //  Initiate curl
        $ch = curl_init();
// Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
// Execute
        $results = curl_exec($ch);
        $results = json_decode($results, true);
// Closing
        curl_close($ch);

// Will dump a beauty json :3
        // var_dump(json_decode($result, true));
        foreach ($results as $key => $result) {
            # code...
            $brand = new Brand();
            $brand->name = $result["brand"];
            $brand->save();

            foreach ($result["models"] as $key_model => $carmodel) {
                # code...
                $model = new CarModel();
                $model->car_brand_id = $brand->id;
                $model->model_name = $carmodel;
                $model->save();
            }
        }
    }
}
