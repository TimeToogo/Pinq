<?php

namespace Pinq\Caching;

use Pinq\PinqException;

/**
 * Cache implementation for storing the serialized expression trees
 * each in their own file in the specified directory
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DirectoryCache extends CacheAdapter
{
    const DEFAULT_EXTENSION = '.cached';

    /**
     * The root directory to store the files.
     *
     * @var string
     */
    private $rootDirectory;

    /**
     * The directory of the namespace to store the files
     *
     * @var string
     */
    private $directory;

    /**
     * The extension to use for the stored files
     *
     * @var string
     */
    private $fileExtension;

    public function __construct($directory, $fileExtension = self::DEFAULT_EXTENSION, $namespace = null)
    {
        parent::__construct($namespace);
        $this->rootDirectory = $directory . DIRECTORY_SEPARATOR;
        $this->fileExtension = $fileExtension;

        if ($namespace !== null) {
            $this->directory = $this->rootDirectory . md5($namespace) . DIRECTORY_SEPARATOR;
        } else {
            $this->directory = $this->rootDirectory;
        }

        if (!is_dir($this->directory)) {
            if (!@mkdir($this->directory, 0777, true)) {
                throw new PinqException(
                        'Invalid cache directory: %s does not exist and could not be created',
                        $this->directory);
            }
        }
    }

    public function forNamespace($namespace)
    {
        $cache            = new self($this->rootDirectory, $this->fileExtension, $namespace);
        $cache->namespace = $namespace;

        return $cache;
    }

    public function inGlobalNamespace()
    {
        return new self($this->rootDirectory, $this->fileExtension);
    }

    public function save($key, $value)
    {
        file_put_contents($this->getCacheFilePath($key), serialize($value));
    }

    private function getCacheFilePath($key)
    {
        return $this->directory . md5($key) . $this->fileExtension;
    }

    public function contains($key)
    {
        return is_readable($this->getCacheFilePath($key));
    }

    public function tryGet($key)
    {
        $filePath = $this->getCacheFilePath($key);

        if (!is_readable($filePath)) {
            return null;
        }

        return unserialize(file_get_contents($filePath));
    }

    public function clear()
    {
        self::deleteDirectoryFiles($this->directory);
    }

    protected static function deleteDirectoryFiles($directory)
    {
        foreach (glob($directory . DIRECTORY_SEPARATOR . '*') as $file) {
            if (is_dir($file)) {
                self::deleteDirectoryFiles($file);
            } else {
                unlink($file);
            }
        }
    }

    public function remove($key)
    {
        $filePath = $this->getCacheFilePath($key);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
