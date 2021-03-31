<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommender extends Model
{
    use HasFactory;
    protected $table = 'recommenders';
	  protected $fillable = ['user_id', 'parent_step1_id', 'parent_step2_id','parent_step1_id_string','parent_step2_id_string'];
}
