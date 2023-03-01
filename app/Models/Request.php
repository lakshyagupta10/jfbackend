<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
  public function up()
  {
  Schema::create("requests",function(Blueprint $table){
   $table->increments("id");
   $table->text("request");
   $table->text("response");
   $table->string("url", 1024);
   $table->string("ip", 16);
   $table->timestamps();
  });
}}
