<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultConfirmedStateAndRenameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('confirm_token', 'confirm_tokens');
        Schema::table('confirm_tokens', static function (Blueprint $table) {
            $table->boolean('confirmed')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('confirm_tokens', 'confirm_token');
    }
}
