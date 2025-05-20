<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * The database schema.
     *
     * @var \Illuminate\Support\Facades\Schema
     */
    protected $schema;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new migration instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Tenant bağlantısını kullan
        $this->schema = Schema::connection(config('database.default'));

        // Tablo adını ayarla
        $this->table = 'views';
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!$this->schema->hasTable($this->table)) {
            $this->schema->create($this->table, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->morphs('viewable');
                $table->text('visitor')->nullable();
                $table->string('collection')->nullable();
                $table->timestamp('viewed_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
};