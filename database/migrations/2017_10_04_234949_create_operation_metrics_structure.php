<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationMetricsStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Panel
        Schema::create('operation_panels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->unsigned();
            $table->string('service_type', 20);
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
        });

        // Panel Users
        Schema::create('operation_panel_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('panel_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('panel_id')->references('id')->on('operation_panels')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        // Segment Type
        Schema::create('operation_segment_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 20);
            $table->string('name', 255);
            $table->timestamps();
            $table->softDeletes();
        });

        // Segments
        Schema::create('operation_segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('panel_id')->unsigned();
            $table->integer('segment_type_id')->unsigned();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('name', 100)->nullable();
            $table->unsignedTinyInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('panel_id')->references('id')->on('operation_panels')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('segment_type_id')->references('id')->on('operation_segment_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('operation_segments')->onUpdate('cascade')->onDelete('cascade');
        });

        // Milestones
        Schema::create('operation_milestones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('segment_id')->unsigned();
            $table->string('name', 80)->nullable();
            $table->tinyInteger('days')->nullable();
            $table->unsignedTinyInteger('warning1')->nullable();
            $table->unsignedTinyInteger('warning2')->nullable();
            $table->unsignedTinyInteger('critical1')->nullable();
            $table->unsignedTinyInteger('critical2')->nullable();
            $table->unsignedTinyInteger('critical3')->nullable();
            $table->unsignedTinyInteger('critical4')->nullable();
            $table->unsignedTinyInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('segment_id')->references('id')->on('operation_segments')->onUpdate('cascade')->onDelete('cascade');
        });

        // Milestone - Checkpoint Codes
        Schema::create('operation_milestone_checkpoint_code', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('checkpoint_code_id')->unsigned();
            $table->integer('milestone_id')->unsigned();
            $table->timestamps();

            $table->foreign('checkpoint_code_id')->references('id')->on('checkpoint_codes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('milestone_id')->references('id')->on('operation_milestones')->onUpdate('cascade')->onDelete('cascade');
        });

        // Frequencies
        Schema::create('operation_frequencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 10);
            $table->string('value', 20);
            $table->timestamps();
            $table->softDeletes();
        });

        // Batches
        Schema::create('operation_batches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('frequency_id')->unsigned();
            $table->string('value');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('frequency_id')->references('id')->on('operation_frequencies')->onUpdate('cascade')->onDelete('cascade');
        });

        // Metrics
        Schema::create('operation_metrics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('batch_id')->unsigned();
            $table->integer('milestone_id')->unsigned();
            $table->integer('package_id')->unsigned();

            // Transit stats
            $table->unsignedSmallInteger('stalled')->nullable();
            $table->unsignedSmallInteger('controlled')->nullable();
            $table->unsignedSmallInteger('total')->nullable();

            $table->timestamps();

            $table->foreign('batch_id')->references('id')->on('operation_batches')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('milestone_id')->references('id')->on('operation_milestones')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
        });

        // States Milestones
        Schema::create('operation_state_milestones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('segment_id')->unsigned();
            $table->integer('state_id')->unsigned();
            $table->unsignedTinyInteger('transit');
            $table->unsignedTinyInteger('distribution');
            $table->unsignedTinyInteger('warning1')->nullable();
            $table->unsignedTinyInteger('warning2')->nullable();
            $table->unsignedTinyInteger('critical1')->nullable();
            $table->unsignedTinyInteger('critical2')->nullable();
            $table->unsignedTinyInteger('critical3')->nullable();
            $table->unsignedTinyInteger('critical4')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('segment_id')->references('id')->on('operation_segments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('state_id')->references('id')->on('states')->onUpdate('cascade')->onDelete('cascade');
        });

        // State Batches
        Schema::create('operation_state_batches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('frequency_id')->unsigned();
            $table->string('value');
            $table->timestamp('created_at')->nullable();
            $table->softDeletes();

            $table->foreign('frequency_id')->references('id')->on('operation_frequencies')->onUpdate('cascade')->onDelete('cascade');
        });

        // State Metrics
        Schema::create('operation_state_milestone_metrics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('state_batch_id')->unsigned();
            $table->integer('state_milestone_id')->unsigned();
            $table->integer('package_id')->unsigned();
            $table->timestamp('created_at')->nullable();

            // Transit stats
            $table->unsignedSmallInteger('en_route')->nullable();
            $table->unsignedSmallInteger('stalled')->nullable();
            $table->unsignedSmallInteger('controlled')->nullable();
            $table->unsignedSmallInteger('customs_to_postal_office')->nullable();
            $table->unsignedSmallInteger('mailman')->nullable();
            $table->unsignedSmallInteger('total')->nullable();

            $table->foreign('state_batch_id')->references('id')->on('operation_state_batches')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('state_milestone_id')->references('id')->on('operation_state_milestones')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
        });

        // Performance Formulas
        Schema::create('operation_performance_formulas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 20);
            $table->string('name', 40);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Panel Performance
        Schema::create('operation_performances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('panel_id')->unsigned();
            $table->integer('performance_formula_id')->unsigned();
            $table->decimal('minimum');
            $table->decimal('maximum');
            $table->decimal('average')->nullable();
            $table->decimal('mean')->nullable();
            $table->decimal('percentile60')->nullable();
            $table->decimal('percentile75')->nullable();
            $table->decimal('percentile90')->nullable();
            $table->decimal('std_dev')->nullable();
            $table->integer('package_count')->unsigned();
            $table->text('frequencies')->nullable();
            $table->timestamp('period_from');
            $table->timestamp('period_to');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('panel_id')->references('id')->on('operation_panels')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('performance_formula_id')->references('id')->on('operation_performance_formulas')->onUpdate('cascade')->onDelete('cascade');
        });

        // Panel States performance
        Schema::create('operation_state_performances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('panel_id')->unsigned();
            $table->integer('performance_formula_id')->unsigned();
            $table->integer('state_id')->unsigned();
            $table->decimal('minimum');
            $table->decimal('maximum');
            $table->decimal('average')->nullable();
            $table->decimal('mean')->nullable();
            $table->decimal('percentile60')->nullable();
            $table->decimal('percentile75')->nullable();
            $table->decimal('percentile90')->nullable();
            $table->decimal('std_dev')->nullable();
            $table->integer('package_count')->unsigned();
            $table->text('frequencies')->nullable();
            $table->timestamp('period_from');
            $table->timestamp('period_to');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('panel_id')->references('id')->on('operation_panels')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('performance_formula_id')->references('id')->on('operation_performance_formulas')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('state_id')->references('id')->on('states')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('operation_holidays', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->unsigned();
            $table->date('date');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('operation_holidays');
        Schema::drop('operation_state_performances');
        Schema::drop('operation_performances');
        Schema::drop('operation_performance_formulas');
        Schema::drop('operation_state_milestone_metrics');
        Schema::drop('operation_state_batches');
        Schema::drop('operation_state_milestones');
        Schema::drop('operation_metrics');
        Schema::drop('operation_batches');
        Schema::drop('operation_frequencies');
        Schema::drop('operation_milestone_checkpoint_code');
        Schema::drop('operation_milestones');
        Schema::drop('operation_segments');
        Schema::drop('operation_segment_types');
        Schema::drop('operation_panel_user');
        Schema::drop('operation_panels');
    }
}
