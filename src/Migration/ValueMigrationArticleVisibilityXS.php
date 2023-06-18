<?php

declare(strict_types=1);

/*
 * This file is part of Schuldnerberatung.
 *
 * (c) Christian Romeni 2022 <christian@romeni.eu>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/rwd/contao-sb-bundle
 */

namespace Rwd\ContaoCustomArticlesBundle\Migration;

// use Contao\CoreBundle\Migration\AbstractMigration;
// use Contao\CoreBundle\Migration\MigrationResult;

use Contao\CoreBundle\Migration\MigrationInterface;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class ValueMigrationArticleVisibilityXS extends AbstractMigration implements MigrationInterface
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function shouldRun(): bool
    {
        // Überprüfe hier deine Bedingungen
        $schemaManager = $this->connection->getSchemaManager();
        if (!$schemaManager->tablesExist(['tl_article'])) {
            return false;
        }

        $result = $this->connection->executeQuery("SELECT COUNT(*) AS count FROM tl_article WHERE article_visible LIKE '%visible-xs%'");
        $row = $result->fetchAssociative();

        return ($row['count'] > 0);
    }

    public function run(): MigrationResult
    {
        // Führe hier deine Migrationsschritte durch
        $this->connection->executeUpdate("UPDATE tl_article SET article_visible = REPLACE(article_visible, 'visible-xs', 'd-none d-sm-block') WHERE article_visible LIKE '%visible-xs%'");
        
        // Erstelle das Migrationsergebnis
        $result = $this->createResult(
            true,
            'Das Feld article_visible wurde erfolgreich aktualisiert.'
        );
        
        return $result;
    }

    public function getName(): string
    {
        return 'ValueMigrationArticleVisibilityXS';
    }
}