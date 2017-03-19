<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds user.projects_order_by_asc column.
 */
class Version20170319134407 extends AbstractMigration
{
	/**
	 * @param Schema $schema
	 */
	public function up(Schema $schema)
	{
		$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql',
			'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE "user" ADD projects_order_by_asc BOOLEAN DEFAULT \'true\' NOT NULL');
	}

	/**
	 * @param Schema $schema
	 */
	public function down(Schema $schema)
	{
		$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql',
			'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE "user" DROP projects_order_by_asc');
	}
}
