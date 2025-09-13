<?php
/**
 * @author Kemoy Campbell
 * Date: 12/27/18
 * Time: 11:54 AM
 */

namespace Bolzen\Core\Plugin;

interface PluginInterface
{
    /**
     * Get the plugin name
     * @return string
     */
    public function getName(): string;

    /**
     * Check if the plugin is enabled in the configuration
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Load the plugin and register it with the dependency injection container
     * @param mixed $container The DI container
     * @return void
     */
    public function load($container): void;
}