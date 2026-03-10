<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IndiceMonetario extends Model {
    protected $table = "indices_monetarios";
    protected $fillable = ["nome", "sigla", "mes_ref", "percentual"];
    protected $casts = ["mes_ref" => "date"];
}