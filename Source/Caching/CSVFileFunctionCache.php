<?php 

namespace Pinq\Caching;

use Pinq\FunctionExpressionTree;

/**
 * Cache implementation for storing the serialized expression trees
 * in a single file in a csv format, not very good for concurrency
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CSVFileFunctionCache implements IFunctionCache
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
            $this->fileHandle->setCsvControl(
                    self::CSV_DELIMITER,
                    self::CSV_SEPERATOR);
        } 
        catch (\Exception $Exception) {
            throw new \Pinq\PinqException(
                    'Invalid cache file: %s is not readable with the message, "%s"',
                    $fileName,
                    $exception->getMessage());
        }
        
        $this->fileName = $fileName;
    }
    
    private function &getFileData()
    {
        if ($this->fileData === null) {
            $this->fileData = [];
            
            foreach ($this->fileHandle as $row) {
                if (count($row) < 2) {
                    continue;
                }
                
                list($functionHash, $serializedExpressionTree) = $row;
                $this->fileData[$functionHash] = $serializedExpressionTree;
            }
        }
        
        return $this->fileData;
    }
    
    public function save($functionHash, FunctionExpressionTree $functionExpressionTree)
    {
        $fileData =& $this->getFileData();
        $serializedFunctionExpressionTree = serialize($functionExpressionTree);
        
        if (isset($fileData[$functionHash])) {
            if ($fileData[$functionHash] === $serializedFunctionExpressionTree) {
                return;
            }
        }
        
        $fileData[$functionHash] = $serializedFunctionExpressionTree;
        $this->flushFileData();
    }
    
    public function tryGet($functionHash)
    {
        $fileData = $this->getFileData();
        
        if (!isset($fileData[$functionHash])) {
            return null;
        }
        
        return unserialize($fileData[$functionHash]);
    }
    
    public function clear()
    {
        $this->fileData = [];
        $this->flushFileData();
    }
    
    public function remove($functionHash)
    {
        $fileData =& $this->getFileData();
        
        if (!isset($fileData[$functionHash])) {
            return;
        }
        
        unset($fileData[$functionHash]);
        $this->flushFileData();
    }
    
    private function flushFileData()
    {
        $fileHandle = $this->fileHandle;
        
        if ($fileHandle->flock(LOCK_EX)) {
            $fileHandle->fseek(0, SEEK_SET);
            $fileHandle->ftruncate(0);
            
            foreach ($this->getFileData() as $signature => $serializedExpressionTree) {
                $fileHandle->fputcsv([$signature, $serializedExpressionTree]);
            }
            
            $fileHandle->flock(LOCK_UN);
        }
    }
    
    public function __destruct()
    {
        $this->fileHandle = null;
    }
}