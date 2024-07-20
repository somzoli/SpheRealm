<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Settings;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('option')->primary();
            $table->string('label')->nullable();
            $table->string('description')->nullable();
            $table->text('value')->nullable();
            // For ldap connection
            /*$table->text('ldap_connection')->nullable();
            $table->text('ldap_host')->nullable();
            $table->text('ldap_username')->nullable();
            $table->text('ldap_password')->nullable();
            $table->text('ldap_basedn')->nullable();
            $table->text('ldap_ssl')->nullable();
            $table->text('ldap_tls')->nullable();*/
            $table->timestamps();
        });

        Settings::create([
            'option' => 'ldap_admin_users',
            'label' => 'Ldap Admins Users Group DN',
            'description' => 'Admin Users Group From LDAP/AD',
            'value' => null,
        ]);
        Settings::create([
            'option' => 'ldap_servers',
            'label' => 'Ldap Servers Group DN',
            'description' => 'Servers Group From LDAP/AD',
            'value' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
