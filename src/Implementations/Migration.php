<?php

namespace Telus\Digital\Libraries\DarkLaunch\Implementations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration {
    /** @var \Illuminate\Database\Capsule\Manager $capsule */
    public $capsule;
    /** @var \Illuminate\Database\Schema\Builder $capsule */
    public $schema;

    /**
     * Information is supplied to this function from .phinx.yml
     * 
     */
    public function init()
    {
      $this->capsule = new Capsule;
      $this->capsule->addConnection([
        'driver'    => 'mysql',
        'host'      => getenv('DB_HOST'),
        'port'      => getenv('DB_PORT'),
        'database'  => getenv('DB_NAME'),
        'username'  => getenv('DB_USER'),
        'password'  => getenv('DB_PASSWORD'),
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
      ]);

      $this->capsule->bootEloquent();
      $this->capsule->setAsGlobal();
      $this->schema = $this->capsule->schema();
    }
}