<?php

use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_manipulations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('path', 2000)->comment('存储路径');
            $table->string('type', 25);
            $table->text('data')->comment('上传的原始数据');
            $table->string('output_path', 2000)->nullable()->comment('输出路径');
            $table->timestamp('created_at')->nullable();
            $table->foreignIdFor(User::class)->nullable();
            $table->foreignIdFor(Album::class)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_manipulations');
    }
};
