<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
	public function up(): void
	{
    		Schema::table('aasp_publicacoes', function (Blueprint $table) {
        	$table->unsignedBigInteger('processo_id')->nullable()->index();
        	$table->foreign('processo_id')->references('id')->on('processos')->nullOnDelete();
    	});
	}

	public function down(): void
	{
    		Schema::table('aasp_publicacoes', function (Blueprint $table) {
        	$table->dropForeign(['processo_id']);
        	$table->dropColumn('processo_id');
    	});
	}
};



