<?php

namespace App\Http\Controllers;

use App\Models\Taxa;
use App\Models\User;
use App\Models\CountForm;
use Illuminate\Http\Request;
use App\Models\IbpObservation;
use App\Models\IfbObservation;
use App\Models\InatObservation;

class ImportController extends Controller
{
    public function import()
    {
        $result = [
            "taxa" => $this->import_taxa(),
            "counts" => $this->import_counts(),
            "inat" => $this->import_inat(),
            "ibp" => $this->import_ibp(),
            "ifb" => $this->import_ifb()
        ];
        dd($result);
    }

    public function import_taxa()
    {
        $existing_taxa = Taxa::pluck('id')->toArray();
        $taxa = json_decode(file_get_contents(public_path('/data/taxa.json')));
        $count = 0;
        foreach($taxa as $t){
            if(!in_array($t->id, $existing_taxa)){
                $taxon = new Taxa();
                $taxon->id = $t->id;
                $taxon->name = $t->name;
                $taxon->common_name = $t->common_name;
                $taxon->rank = $t->rank;
                $taxon->ancestry = $t->ancestry;
                $taxon->save();
                $existing_taxa[] = $t->id;
                $count++;
            }
        }
        return $count;
    }

    public function import_counts()
    {
        $existing_counts = CountForm::pluck('id')->toArray();
        $existing_taxa = Taxa::pluck('id')->toArray();
        $counts = json_decode(file_get_contents(public_path('/data/count_forms.json')));
        $forms = json_decode(file_get_contents(public_path('/data/form_rows.json')));
        $merged = [];
        $add_count = [
            "forms" => 0,
            "rows" => 0
        ];
        foreach ($counts as $count) {
            $merged[$count->id] = $count;
        }
        foreach($forms as $form){
            $merged[$form->count_form_id]->rows[] = $form;
        }
        foreach($merged as $form){
            if(!in_array($form->id, $existing_counts)){
                $count = new CountForm();
                $user_id = $this->create_or_get_user_id("count", null, $form->name);
                $count->id = $form->id;
                $count->user_id = $user_id;
                $count->name = $form->name;
                $count->affiliation = $form->affiliation;
                $count->phone = $form->phone;
                $count->email = $form->email;
                $count->team_members = $form->team_members;
                $count->photo_link = $form->photo_link;
                $count->location = $form->location;
                $count->state = $form->state;
                $count->district = $form->district;
                $count->coordinates = $form->coordinates;
                $count->latitude = $form->latitude;
                $count->longitude = $form->longitude;
                $count->date = $form->date;
                $count->date_cleaned = $form->date_cleaned;
                $count->start_time = $form->start_time;
                $count->end_time = $form->end_time;
                $count->altitude = $form->altitude;
                $count->distance = $form->distance;
                $count->weather = $form->weather;
                $count->comments = $form->comments;
                $count->file = $form->file;
                $count->original_filename = $form->original_filename;
                $count->duplicate = $form->duplicate;
                $count->validated = $form->validated;
                $count->flag = $form->flag;
                $count->created_at = $form->created_at;
                $count->updated_at = $form->updated_at;
                $count->save();
                $add_count["forms"]++;
                $existing_counts[] = $form->id;
                foreach($form->rows as $row){
                    $taxa_id = null;
                    if(in_array($row->inat_taxa_id, $existing_taxa)){
                        $taxa_id = $row->inat_taxa_id;
                    }
                    $add_count["rows"]++;
                    $count->rows()->create([
                        'count_form_id' => $count->id,
                        'sl_no' => $row->sl_no,
                        'common_name' => $row->common_name,
                        'scientific_name' => $row->scientific_name,
                        'taxa_id' => $taxa_id,
                        'individuals' => $row->individuals,
                        'no_of_individuals_cleaned' => $row->no_of_individuals_cleaned,
                        'remarks' => $row->remarks,
                        'id_quality' => $row->id_quality,
                        'flag' => $row->flag,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]);
                }
            }
            
        }
        return $add_count;
    }

    public function import_inat()
    {
        $existing_inat = InatObservation::pluck('id')->toArray();
        $existing_taxa = Taxa::pluck('id')->toArray();
        $inat_data = json_decode(file_get_contents(public_path('/data/inat.json')));
        $count = 0;
        foreach($inat_data as $i){
            if(!in_array($i->id, $existing_inat)){
                $user_id = $this->create_or_get_user_id("inat", null, $i->user_name);
                $taxa_id = null;
                $location = explode(",",$i->location);
                if(in_array($i->taxa_id, $existing_taxa)){
                    $taxa_id = $i->taxa_id;
                }
                $inat = new InatObservation();
                $inat->id = $i->id;
                $inat->user_id = $user_id;
                $inat->taxa_id = $taxa_id;
                $inat->observed_on = $i->observed_on;
                $inat->location = $i->location;
                $inat->latitude = $location[0];
                $inat->longitude = $location[1];
                $inat->place_guess = $i->place_guess;
                $inat->state = $i->state;
                $inat->district = $i->district;
                $inat->img_url = $i->img_url;
                $inat->is_lepidoptera = $i->is_lepidoptera;
                $inat->description = $i->description;
                $inat->quality_grade = $i->quality_grade;
                $inat->license_code = $i->license_code;
                $inat->oauth_application_id = $i->oauth_application_id;
                $inat->inat_created_at = $i->inat_created_at;
                $inat->inat_updated_at = $i->inat_updated_at;
                $inat->created_at = $i->created_at;
                $inat->updated_at = $i->updated_at;
                $inat->save();
                $count++;
            }
        }
        return $count;
    }

    public function import_ibp()
    {
        $existing_ibp = IbpObservation::pluck('id')->toArray();
        $existing_taxa = Taxa::pluck('id')->toArray();
        $ibp_data = json_decode(file_get_contents(public_path('/data/ibp.json')));
        $count = 0;
        //check if dates are within range
        foreach($ibp_data as $i){
            if(!in_array($i->id, $existing_ibp)){
                $user_id = $this->create_or_get_user_id("ibp", null, $i->createdBy);
                $taxa_id = null;
                if(in_array($i->taxa_id, $existing_taxa)){
                    $taxa_id = $i->taxa_id;
                }
                $ibp = new IbpObservation();
                $ibp->id = $i->id;
                $ibp->user_id = $user_id;
                $ibp->taxa_id = $taxa_id;
                $ibp->createdBy = $i->createdBy;
                $ibp->placeName = $i->placeName;
                $ibp->flag_notes = $i->flagNotes;
                $ibp->createdOn = $i->createdOn;
                $ibp->associatedMedia = $i->associatedMedia;
                $ibp->latitude = $i->locationLat;
                $ibp->longitude = $i->locationLon;
                $ibp->date = $i->fromDate;
                $ibp->rank = $i->rank;
                $ibp->scientificName = $i->scientificName;
                $ibp->commonName = $i->commonName;
                $ibp->state = $i->state;
                $ibp->district = $i->district;
                $ibp->observedInMonth = $i->observedInMonth;
                $ibp->scientific_name_cleaned = $i->scientific_name_cleaned;
                $ibp->created_at = $i->created_at;
                $ibp->updated_at = $i->updated_at;
                $ibp->save();
                $count++;
            }
        }
        return $count;
    }

    public function import_ifb()
    {
        $existing_ifb = IfbObservation::pluck('id')->toArray();
        $existing_taxa = Taxa::pluck('id')->toArray();
        $ifb_data = json_decode(file_get_contents(public_path('/data/ifb.json')));
        $count = 0;
        //check if dates are within range
        foreach($ifb_data as $i){
            if(!in_array($i->id, $existing_ifb)){
                $user_id = $this->create_or_get_user_id("ifb", $i->user);
                $taxa_id = null;
                if(in_array($i->inat_taxa_id, $existing_taxa)){
                    $taxa_id = $i->inat_taxa_id;
                }
                $ifb = new IfbObservation();
                $ifb->boi_id = $i->boi_id;
                $ifb->user_id = $user_id;
                $ifb->taxa_id = $taxa_id;
                $ifb->created_date = $i->created_date;
                $ifb->observed_date = $i->observed_date;
                $ifb->media_code = $i->media_code;
                $ifb->species_name = $i->species_name;
                $ifb->rank = $i->rank;
                $ifb->user_name = $i->user;
                $ifb->life_stage = $i->life_stage;
                $ifb->country = $i->country;
                $ifb->state = $i->state;
                $ifb->district = $i->district;
                $ifb->location_name = $i->location_name;
                $ifb->latitude = $i->latitude;
                $ifb->longitude = $i->longitude;
                $ifb->flag = $i->flag;
                $ifb->created_at = $i->created_at;
                $ifb->updated_at = $i->updated_at;
                $ifb->save();
                $count++;
            }
        }
        return $count;
    }

    public function create_or_get_user_id(String $table, String $login = null, String $name = null)
    {
        $matching_users = null;
        if($login != null){
            $matching_users = User::where("source", $table)->where("login", $login)->get();
        } else if($name != null){
            $matching_users = User::where("source", $table)->where("name", $name)->get();
        }
        if($matching_users->count() == 0){
            $user = new User();
            $user->name = $name;
            $user->login = $login;
            $user->source = $table;
            $user->save();
            return $user->id;
        }
        if ($matching_users->count() == 1){
            return $matching_users->first()->id;
        } else {
            dd("Duplicate", [$table, $login, $name]);
        }
    }
}
