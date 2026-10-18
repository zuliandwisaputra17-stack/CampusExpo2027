<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->boolean('is_admin')->default(false);
        });
        Schema::create('event_editions', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->unsignedInteger('year');
            $t->string('slug')->unique();
            $t->dateTime('event_start_at');
            $t->dateTime('event_end_at');
            $t->string('venue_name');
            $t->text('venue_address');
            $t->text('description');
            $t->string('hero_title');
            $t->text('hero_description');
            $t->string('school_image')->nullable();
            $t->boolean('is_active')->default(false)->index();
            $t->timestamps();
        });
        Schema::create('campuses', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('abbreviation');
            $t->string('logo')->nullable();
            $t->string('cover_image')->nullable();
            $t->string('city');
            $t->string('province')->nullable();
            $t->text('description')->nullable();
            $t->string('official_url');
            $t->integer('display_order')->default(0);
            $t->boolean('is_active')->default(true)->index();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('members', function (Blueprint $t) {
            $t->id();
            $t->foreignId('event_edition_id')->constrained()->restrictOnDelete();
            $t->foreignId('campus_id')->constrained()->restrictOnDelete();
            $t->string('name');
            $t->string('slug');
            $t->string('photo')->nullable();
            $t->string('faculty')->nullable();
            $t->string('major')->nullable();
            $t->string('study_program');
            $t->unsignedInteger('cohort_year')->nullable();
            $t->text('bio')->nullable();
            $t->string('instagram_url')->nullable();
            $t->string('linkedin_url')->nullable();
            $t->integer('display_order')->default(0);
            $t->boolean('is_active')->default(true)->index();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('schedules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('event_edition_id')->constrained()->cascadeOnDelete();
            $t->date('event_date')->index();
            $t->time('start_time');
            $t->time('end_time');
            $t->string('title');
            $t->text('description')->nullable();
            $t->string('location')->nullable();
            $t->string('speaker')->nullable();
            $t->integer('display_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        foreach (['galleries', 'faqs', 'partners'] as $table) {
            Schema::create($table, function (Blueprint $t) use ($table) {
                $t->id();
                $t->foreignId('event_edition_id')->nullable()->constrained()->cascadeOnDelete();
                if ($table === 'galleries') {
                    $t->string('image');
                    $t->string('caption')->nullable();
                    $t->string('alt_text');
                }
                if ($table === 'faqs') {
                    $t->string('question');
                    $t->text('answer');
                }
                if ($table === 'partners') {
                    $t->string('name');
                    $t->string('logo')->nullable();
                    $t->string('url')->nullable();
                    $t->string('type')->default('School Partner');
                }
                $t->integer('display_order')->default(0);
                $t->boolean('is_active')->default(true);
                $t->timestamps();
            });
        }
        Schema::create('site_settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['site_settings', 'partners', 'faqs', 'galleries', 'schedules', 'members', 'campuses', 'event_editions'] as $table) {
            Schema::dropIfExists($table);
        } Schema::table('users', fn (Blueprint $t) => $t->dropColumn('is_admin'));
    }
};
