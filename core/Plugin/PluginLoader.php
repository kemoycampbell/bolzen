<?php
/**
 * @author Kemoy Campbell
 * Date: 12/27/18
 * Time: 11:54 AM
 */

namespace Bolzen\Core\Plugin;

use Bolzen\Core\Config\ConfigInterface;

class PluginLoader
{
    private $config;
    private $plugins = [];

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Register a plugin with the loader
     * @param PluginInterface $plugin
     */
    public function registerPlugin(PluginInterface $plugin): void
    {
        $this->plugins[$plugin->getName()] = $plugin;
    }

    /**
     * Load all enabled plugins
     * @param mixed $container The DI container
     */
    public function loadEnabledPlugins($container): void
    {
        foreach ($this->plugins as $plugin) {
            if ($plugin->isEnabled()) {
                $plugin->load($container);
            }
        }
    }

    /**
     * Get a specific plugin by name
     * @param string $name
     * @return PluginInterface|null
     */
    public function getPlugin(string $name): ?PluginInterface
    {
        return $this->plugins[$name] ?? null;
    }

    /**
     * Check if a plugin is loaded and enabled
     * @param string $name
     * @return bool
     */
    public function isPluginEnabled(string $name): bool
    {
        $plugin = $this->getPlugin($name);
        return $plugin !== null && $plugin->isEnabled();
    }
}