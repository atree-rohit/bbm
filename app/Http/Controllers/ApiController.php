<?php

namespace App\Http\Controllers;

use App\Models\Taxa;
use App\Models\User;
use App\Models\CountForm;
use Illuminate\Http\Request;
use App\Models\IbpObservation;
use App\Models\IfbObservation;
use App\Models\InatObservation;

class ApiController extends Controller
{
    public function get_taxa()
    {
        $data = Taxa::select("id", "name", "common_name", "rank", "ancestry")->get();
        $compressed_data = $this->compress_data($data);
        return response($compressed_data)->header('Content-Encoding', 'gzip');
    }

    public function get_users()
    {
        $data = User::select("id", "name", "source")->get();
        $compressed_data = $this->compress_data($data);
        return response($compressed_data)->header('Content-Encoding', 'gzip');
    }

    public function get_observations()
    {
        $data = collect([
            "counts" => collect($this->clean_counts(CountForm::with("rows")->get())),
            "inat" => collect($this->clean_inat(InatObservation::all())),
            "ibp" => collect($this->clean_ibp(IbpObservation::all(), "ibp")),
            "ifb" => collect($this->clean_ifb(IfbObservation::all())),
        ])->map(function ($collection, $source) {
            return $collection->map(function ($item) use ($source) {
                $item["source"] = $source;
                return $item;
            });
        });
        return response(gzencode($data->toJson()))->header('Content-Encoding', 'gzip');
        
    }

    public function compress_data($collection)
    {
        $filtered_data = $this->filter_unused_fields($collection);
        return gzencode($filtered_data->toJson());
    }
    
    public function filter_unused_fields($collection)
    {
        return $collection->map(function ($item, $fields = ['created_at', 'updated_at']) {
            return collect($item)->except($fields)->toArray();
        });
    }


    public function clean_counts($collection)
    {
        $fields = ["user_id", "latitude", "longitude", "state", "district"];
        $cleaned_data = collect();
        foreach($collection as $count_form){
            if($count_form->duplicate == false & $count_form->flag == false ){
                $current_form = [];
                $form_id = $count_form["id"];
                foreach($fields as $f){
                    $current_form[$f] = $count_form->{$f};
                }
                foreach($count_form->rows as $row){
                    $current_form["id"] = $form_id . "-" . $row->id;
                    $current_form["taxa_id"] = $row->taxa_id;
                    $current_form["date"] = $count_form->date_cleaned;
                    $current_form["individuals"] = $row->no_of_individuals_cleaned;
                    $cleaned_data->push($current_form);
                }
            }
        }
        return $cleaned_data;
    }

    public function clean_inat($collection)
    {
        $fields = ["id", "user_id", "taxa_id", "latitude", "longitude", "state", "district"];
        $cleaned_data = collect();
        foreach($collection as $observation){
            if($observation->flag == false){
                $current_observation = [];
                foreach($fields as $f){
                    $current_observation[$f] = $observation->{$f};
                }
                $current_observation["img"] = $observation->img_url;
                $current_observation["date"] = str_replace("/", "-", $observation->observed_on);
                $cleaned_data->push($current_observation);
            }
        }
        return $cleaned_data;
    }

    public function clean_ibp($collection)
    {
        $fields = ["id", "user_id", "taxa_id", "latitude", "longitude", "state", "district"];
        $cleaned_data = collect();
        foreach($collection as $observation){
            if($observation->flag == false){
                $current_observation = [];
                foreach($fields as $f){
                    $current_observation[$f] = $observation->{$f};
                }
                $original_date = $observation->date; 
                $timestamp = strtotime($original_date);                
                // Creating new date format from that timestamp
                $new_date = date("d-m-Y", $timestamp);

                $current_observation["img"] = "https://indiabiodiversity.org/files-api/api/get/raw/observations//" . $observation["associatedMedia"];
                $current_observation["date"] = $new_date;
                $cleaned_data->push($current_observation);
            }
        }
        return $cleaned_data;
        
    }

    public function clean_ifb($collection)
    {
        $fields = ["user_id", "taxa_id", "latitude", "longitude", "state", "district"];
        $cleaned_data = collect();
        foreach($collection as $observation){
            if($observation->flag == false){
                $current_observation = [];
                foreach($fields as $f){
                    $current_observation[$f] = $observation->{$f};
                }
                $current_observation["id"] = $observation->boi_id . "-" . $observation->id;
                $current_observation["date"] = date_format(date_create_from_format('d/m/y', $observation->observed_date), 'd-m-Y');
                $cleaned_data->push($current_observation);
            }
        }
        return $cleaned_data;
    }
}
