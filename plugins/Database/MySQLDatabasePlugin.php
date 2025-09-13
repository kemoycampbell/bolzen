<?php
/**
 * @author Kemoy Campbell
 * Date: 12/27/18
 * Time: 11:54 AM
 */

namespace Bolzen\Plugins\Database;

use Bolzen\Core\Config\ConfigInterface;
use Bolzen\Core\Plugin\PluginInterface;
use Symfony\Component\DependencyInjection\Reference;

class MySQLDatabasePlugin implements PluginInterface
{
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function getName(): string
    {
        return 'mysql_database';
    }

    public function isEnabled(): bool
    {
        return $this->config->isDatabaseRequired();
    }

    public function load($container): void
    {
        if ($this->isEnabled()) {
            // Register the MySQL database implementation
            $container->register('database', MySQLDatabase::class)
                ->setArguments(array(new Reference('config')));
        }
    }
}