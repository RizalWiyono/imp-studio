<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');

            $table->string('thumbnail_path')->nullable();
            $table->string('thumbnail_url')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('meta_keywords', 500)->nullable();

            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedInteger('reading_time')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
        });


        DB::unprepared("
            CREATE TRIGGER articles_before_insert_slug
            BEFORE INSERT ON articles
            FOR EACH ROW
            BEGIN
                IF NEW.slug IS NULL OR NEW.slug = '' THEN
                    SET NEW.slug = LOWER(REPLACE(NEW.title, ' ', '-'));
                END IF;
            END
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
