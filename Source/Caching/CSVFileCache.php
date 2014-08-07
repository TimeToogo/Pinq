<?php

namespace Pinq\Caching;

/**
 * Cache implementation for storing the serialized expression trees
 * in a single file in a csv format, not very good for concurrency
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class CSVFileCache extends CacheAdapter
{
    const CSV_DELIMITER = ',';
    const CSV_SEPERATOR = '|';

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var \SplFileObject
     */
    private $fileHandle;

    /**
     * @var array|null
     */
    private $fileData;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;

        try {
            $this->fileHandle = new \SplFileObject($fileName, 'c+');
            $this->fileHandle->setFlags(\SplFileObject::READ_CSV);
            $this->fileHandle->setCsvControl(self::CSV_DELIMITER, self::CSV_SEPERATOR);
        } catch (\Exception $exception) {
            throw new \Pinq\PinqException(
                    'Invalid cache file: %s is not readable with the message, "%s"',
                    $fileName,
                    $exception->getMessage());
        }

        $this->fileName = $fileName;
    }

    public function save($key, $value)
    {
        $fileData        =& $this->getFileData();
        $serializedValue = serialize($value);

        if (isset($fileData[$key])) {
            if ($fileData[$key] === $serializedValue) {
                return;
            }
        }

        $fileData[$key] = $serializedValue;
        $this->flushFileData();
    }

    private function &getFileData()
    {
        if ($this->fileData === null) {
            $this->fileData = [];

            foreach ($this->fileHandle as $row) {
                if (count($row) < 2) {
                    continue;
                }

                list($key, $serializedExpressionTree) = $row;
                $this->fileData[$key] = $serializedExpressionTree;
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

        return array_key_exists($key, $fileData);
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
        $fileData =& $this->getFileData();

        if (!isset($fileData[$key])) {
            return;
        }

        unset($fileData[$key]);
        $this->flushFileData();
    }

    public function clear($namespace = null)
    {
        if ($namespace === null) {
            $this->fileData = [];
        } else {
            $fileData =&  $this->getFileData();
            foreach ($fileData as $key => $value) {
                if (strpos($key, $namespace) === 0) {
                    unset($fileData[$key]);
                }
            }
        }

        $this->flushFileData();
    }

    public function __destruct()
    {
        $this->fileHandle = null;
    }
}
