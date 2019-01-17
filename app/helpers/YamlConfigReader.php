<?php
namespace DmServer;

use Symfony\Component\Yaml\Yaml;

class YamlConfigReader
{
    public static function parse($file, $callback = null)
    {
        $config = Yaml::parse(file_get_contents($file));
        if (is_array($config)) {
            self::importSearch($config, $file);
            if (isset($callback)) {
                $callback($config);
            }
            return $config;
        }
        return null;
    }
    public static function importSearch(&$config, $file)
    {
        foreach ($config as $key => $value) {
            if ($key === 'imports') {
                foreach ($value as $resource) {
                    $base_dir = str_replace(basename($file), '', $file);
                    $new_config = self::parse($base_dir . $resource['resource']);
                }
                unset($config['imports']);
            }
        }
    }
}