<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHumanResourcesTablesPartFour extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('HR_Department', function(Blueprint $table)
      {
        //Foreign Key
        $table->unsignedInteger('raw_materials_warehouse_id')->nullable();//Bodega Materia prima
        $table->foreign('raw_materials_warehouse_id')->references('id')->on('INV_Warehouse');
        $table->unsignedInteger('finished_goods_warehouse_id')->nullable();//Bodega Productos terminados
        $table->foreign('finished_goods_warehouse_id')->references('id')->on('INV_Warehouse');
        $table->unsignedInteger('consumable_warehouse_id')->nullable();//Bodega Consumibles
        $table->foreign('consumable_warehouse_id')->references('id')->on('INV_Warehouse');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      // Schema::drop('');
    }
}
