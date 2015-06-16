<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('notifications', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->string('type');
			$table->string('subject');
			$table->text('body');
			$table->integer('object_id');
			$table->string('object_type');
			$table->boolean('is_read')->default(0);
			$table->integer('from_id');
			$table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('notifications');
    }
}
