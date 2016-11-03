<?php

use Telus\Digital\Libraries\DarkLaunch\Implementations\Migration;

class DatabaseSetup extends Migration
{
  /**
   * Change Method.
   *
   * Write your reversible migrations using this method.
   *
   * More information on writing migrations is available here:
   * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
   *
   * The following commands can be used in this method and Phinx will
   * automatically reverse them when rolling back:
   *
   *    createTable
   *    renameTable
   *    addColumn
   *    renameColumn
   *    addIndex
   *    addForeignKey
   *
   * Remember to call "create()" or "update()" and NOT "save()" when working
   * with the Table class.
   */
  public function change()
  {
    $table = $this->table('keys_to_values');
    $table->addColumn('key', 'string')
          ->addColumn('value', 'text')
          ->addColumn('created_at', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
          ->addColumn('updated_at', 'timestamp')
          ->create();
  }
}
