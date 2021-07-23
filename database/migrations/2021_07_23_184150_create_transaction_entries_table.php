<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_account_id')->nullable()->constrained('accounts');
            $table->foreignId('debit_account_id')->nullable()->constrained('accounts');
            $table->string('external_account_name')->nullable();
            $table->string('external_account_number')->nullable();
            $table->string('external_bank_code')->nullable();
            $table->bigInteger('amount');
            $table->string('reference')->unique();
            $table->string('description')->nullable();
            $table->string('type');
            $table->string('status');
            $table->string('currency')->default('NGN');
            $table->json('meta_data')->nullable();
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
        Schema::dropIfExists('transaction_entries');
    }
}
