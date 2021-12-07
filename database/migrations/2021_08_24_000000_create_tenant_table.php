
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantTable extends Migration
{
    public function up()
    {
        Schema::create('tenants', function (Blueprint $blueprint) {
            $blueprint->string('client_key', 36)->primary();
            $blueprint->string('key');
            $blueprint->text('shared_secret');
            $blueprint->string('base_url');
            $blueprint->string('display_url')->nullable();
            $blueprint->string('display_url_servicedesk_help_center')->nullable();
            $blueprint->string('product_type', 10);
            $blueprint->text('description');

            $blueprint->timestamps();
            $blueprint->timestamp('disabled_at')->nullable();
            $blueprint->timestamp('uninstalled_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenants');
    }
}