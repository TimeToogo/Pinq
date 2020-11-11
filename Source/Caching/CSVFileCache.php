<?php

namespace Pinq\Caching;

use Pinq\PinqException;

/**
 * Cache implementation for storing the serialized expression trees
 * in a single file in a csv format, not very good for concurrency
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class CSVFileCache extends OneDimensionalCacheAdapter
{
    const CSV_DELIMITER = ',';
    const CSV_SEPARATOR = '|';

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var \SplFileObject
     */
    private $fileHandle;

    /**
     * @var \ArrayObject|null
     */
    private $fileData;

    public function __construct($fileName)
    {
        parent::__construct();
        $this->fileName = $fileName;

        try {
            $this->fileHandle = new \SplFileObject($fileName, 'c+');
            $this->fileHandle->setFlags(\SplFileObject::READ_CSV);
            $this->fileHandle->setCsvControl(self::CSV_DELIMITER, self::CSV_SEPARATOR);
        } catch (\Exception $exception) {
            throw new PinqException(
                    'Invalid cache file: %s is not readable with the message, "%s"',
                    $fileName,
                    $exception->getMessage());
        }

        $this->fileName = $fileName;
    }

    public function save($key, $value)
    {
        $fileData        = $this->getFileData();
        $serializedValue = serialize($value);

        if (isset($fileData[$key])) {
            if ($fileData[$key] === $serializedValue) {
                return;
            }
        }

        $fileData[$key] = $serializedValue;
        $this->flushFileData();
    }

    private function getFileData()
    {
        if ($this->fileData === null) {
            $this->fileData = new \ArrayObject();

            foreach ($this->fileHandle as $row) {
                if (count($row) < 2) {
                    continue;
                }

                list($key, $serializedValue) = $row;
                $this->fileData[$key] = $serializedValue;
            }
        }

        return $this->fileData;
    }

    private function flushFileData()
    {
        $fileHandle = $this->fileHandle;

        if ($fileHandle->flock(LOCK_EX)) {
            $fileHandle->fseek(0, SEEK_SET);
            $fileHandle->ftruncate(0);

            foreach ($this->getFileData() as $signature => $serializedValue) {
                $fileHandle->fputcsv([$signature, $serializedValue]);
            }

            $fileHandle->flock(LOCK_UN);
        }
    }

    public function contains($key)
    {
        $fileData = $this->getFileData();

        return $fileData->offsetExists($key);
    }

    public function tryGet($key)
    {
        $fileData = $this->getFileData();

        if (!isset($fileData[$key])) {
            return null;
        }

        return unserialize($fileData[$key]);
    }

    public function remove($key)
    {
        $fileData = $this->getFileData();

        if (!isset($fileData[$key])) {
            return;
        }

        unset($fileData[$key]);
        $this->flushFileData();
    }

    public function clear()
    {
        $this->fileData = new \ArrayObject();
        $this->flushFileData();
    }

    public function clearInNamespace($namespace)
    {
        $fileData = $this->getFileData();
        $keysToUnset = [];
        foreach ($fileData as $key => $value) {
            if (strpos($key, $namespace) === 0) {
                $keysToUnset[] = $key;
            }
        }

        foreach ($keysToUnset as $key) {
            unset($fileData[$key]);
        }

        $this->flushFileData();
    }

    public function __destruct()
    {
        $this->fileHandle = null;
    }
}
