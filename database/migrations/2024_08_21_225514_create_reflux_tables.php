<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->unique(['name']);
            $table->string('iidxid')->after('name')->nullable();
            $table->string('infinitasid')->after('iidxid')->nullable();
            $table->string('apikey')->after('password')->unique();
            $table->string('scope')->after('apikey')->default('private');
        });

        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->string('title2', 255)->nullable();
            $table->string('genre', 255)->nullable();
            $table->string('artist', 255)->nullable();
            $table->string('bpm', 10)->nullable();
            $table->string('iidx_id', 255)->nullable();
            $table->string('unlocktype', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('charts', function (Blueprint $table) {
            $table->id();
            $table->string('song_id');
            $table->integer('notecount');
            $table->string('difficulty', 3);
            $table->integer('level');
            $table->boolean('unlocked')->default(false);
            $table->timestamps();
        });

        Schema::create('chartstats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chart_id');
            $table->foreignId('user_id');
            $table->string('grade', 3)->default('NP');
            $table->string('gradediff', 12)->default('NP');
            $table->string('lamp', 3)->default('NP');
            $table->integer('miss')->nullable();
            $table->integer('combobreak')->nullable();
            $table->integer('ex_score')->default(0);
            $table->string('playtype', 2);
            $table->boolean('imported')->default(false);
            $table->integer('nc_gauge')->default(0);
            $table->integer('hc_gauge')->default(0);
            $table->integer('ex_gauge')->default(0);
            $table->integer('nc_endnote')->default(0);
            $table->integer('hc_endnote')->default(0);
            $table->integer('ex_endnote')->default(0);
            $table->double('percent_max')->default(0);
            $table->date('lastplayed')->nullable();
            $table->timestamps();
        });

        Schema::create('plays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chart_id');
            $table->foreignId('user_id');
            $table->string('grade', 3)->default('');
            $table->string('gradediff', 12)->default('');
            $table->string('lamp', 3)->default('');
            $table->integer('pgreat')->default(0);
            $table->integer('great')->default(0);
            $table->integer('good')->default(0);
            $table->integer('bad')->default(0);
            $table->integer('poor')->default(0);
            $table->integer('combobreak')->default(0);
            $table->integer('misscount')->default(0);
            $table->integer('ex_score')->default(0);
            $table->integer('fast')->default(0);
            $table->integer('slow')->default(0);
            $table->datetime('date')->nullable();
            $table->integer('gaugepercent')->default(0);
            $table->string('playtype', 2);
            $table->string('style')->nullable();
            $table->string('style2')->nullable();
            $table->string('gaugetype')->nullable();
            $table->boolean('premature_end')->default(false);
            $table->string('range', 20)->nullable();
            $table->string('assist', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropColumn(['iidxid']);
            $table->dropColumn(['infinitasid']);
            $table->dropColumn(['apikey']);
            $table->dropColumn(['scope']);
        });
        Schema::dropIfExists('songs');
        Schema::dropIfExists('charts');
        Schema::dropIfExists('chartstats');
        Schema::dropIfExists('plays');
    }
};
