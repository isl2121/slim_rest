<?php

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
   protected $table = 'boards';
   protected $fillable = ['subject','content'];
   #public $timestamps = false;content
}

?>