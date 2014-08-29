<?php

namespace Pinq\Caching;

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
     * The directory to store the files
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

    public function __construct($directory, $fileExtension = self::DEFAULT_EXTENSION)
    {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true)) {
                throw new \Pinq\PinqException(
                        'Invalid cache directory: %s does not exist and could not be created',
                        $directory);
            }
        }

        $this->directory     = $directory;
        $this->fileExtension = $fileExtension;
    }

    public function save($key, $value)
    {
        file_put_contents($this->getCacheFilePath($key), serialize($value));
    }

    private function getCacheFilePath($fileName, $suffix = '')
    {
        return $this->directory . DIRECTORY_SEPARATOR . bin2hex($fileName) . $suffix . $this->fileExtension;
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

    public function clear($namespace = null)
    {
        foreach (glob($this->getCacheFilePath($namespace, '*')) as $path) {
            unlink($path);
        }
    }

    public function remove($key)
    {
        unlink($this->getCacheFilePath($key));
    }
}
