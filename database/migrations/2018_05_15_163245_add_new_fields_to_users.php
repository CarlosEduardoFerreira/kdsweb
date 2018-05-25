<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            //$table->integer('parent_id')->after('id');
            //$table->string('username',45)->after('parent_id');

            $table->string('last_name', 100)->after('name');
            //$table->string('business_name', 200)->after('last_name');
            $table->string('dba', 200)->after('business_name');

            $table->string('phone_number', 45)->after('dba');
            $table->text('address')->after('phone_number');
            $table->string('city', 100)->after('address');
            $table->string('state', 100)->after('city');
            $table->string('country', 100)->after('state');
            $table->string('zipcode', 30)->after('country');
            $table->string('timezone', 100)->after('zipcode');

            //$table->integer('licenses_quantity')->after('confirmed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            //$table->dropColumn('parent_id');
            //$table->dropColumn('username');
            $table->dropColumn('last_name');
            //$table->dropColumn('business_name');
            $table->dropColumn('dba');
            $table->dropColumn('phone_number');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('country');
            $table->dropColumn('zipcode');
            $table->dropColumn('timezone');
            //$table->dropColumn('licenses_quantity');
        });
    }
}
