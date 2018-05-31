<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180328150526 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('user');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);
        $table->addColumn('email', 'string', ['notnull'=>true, 'length'=>128]);
        $table->addColumn('full_name', 'string', ['notnull'=>true, 'length'=>512]);
        $table->addColumn('password', 'string', ['notnull'=>true, 'length'=>256]);
        $table->addColumn('status', 'integer', ['notnull'=>true]);
        $table->addColumn('date_created', 'datetime', ['notnull'=>true]);
        $table->addColumn('pwd_reset_token', 'string', ['notnull'=>false, 'length'=>32]);
        $table->addColumn('pwd_reset_token_creation_date', 'datetime', ['notnull'=>false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['email'], 'email_idx');
        //$table->addOption('engine' , 'InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('user');
    }
}
