<?php

namespace Pinq\Caching;

use \Pinq\FunctionExpressionTree;

/**
 * Cache implementation for storing the serialized expression trees
 * in a single file in a csv format, not very good for concurrency
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class CSVFileFunctionCache implements IFunctionCache
{
    const CSVDelimiter = ',';
    const CSVSeperator = '|';
    
    /**
     * @var string
     */
    private $FileName;
    
    /**
     * @var \SplFileObject
     */
    private $FileHandle;
    
    /**
     * @var array|null
     */
    private $FileData;
    
    public function __construct($FileName)
    {
        $this->FileName = $FileName;
        try {
            $this->FileHandle = new \SplFileObject($FileName, 'c+');
            $this->FileHandle->setFlags(\SplFileObject::READ_CSV);
            $this->FileHandle->setCsvControl(self::CSVDelimiter, self::CSVSeperator);
        }
        catch (\Exception $Exception) {
            throw new \Pinq\PinqException('Invalid cache file: %s is not readable with the message, "%s"', $FileName, $Exception->getMessage());
        }
        
        $this->FileName = $FileName;
    }
    
    private function &GetFileData()
    {
        if($this->FileData === null) {
            $this->FileData = [];
            foreach ($this->FileHandle as $Row) {
                if(count($Row) < 2) {
                    continue;
                }
                list($FunctionHash, $SerializedExpressionTree) = $Row;
                $this->FileData[$FunctionHash] = $SerializedExpressionTree;
            }
        }
        
        return $this->FileData;
    }
    
    public function Save($FunctionHash, FunctionExpressionTree $FunctionExpressionTree)
    {
        $FileData =& $this->GetFileData();
        
        $SerializedFunctionExpressionTree = serialize($FunctionExpressionTree);
        if(isset($FileData[$FunctionHash])) {
            if($FileData[$FunctionHash] === $SerializedFunctionExpressionTree) {
                return;
            }
        }
        
        $FileData[$FunctionHash] = $SerializedFunctionExpressionTree;
        $this->FlushFileData();
    }

    public function TryGet($FunctionHash)
    {
        $FileData = $this->GetFileData();
        if(!isset($FileData[$FunctionHash])) {
            return null;
        }
        
        return unserialize($FileData[$FunctionHash]);
    }

    public function Clear()
    {
        $this->FileData = [];
        $this->FlushFileData();
    }

    public function Remove($FunctionHash)
    {
        $FileData =& $this->GetFileData();
        if(!isset($FileData[$FunctionHash])) {
            return;
        }
        unset($FileData[$FunctionHash]);
        
        $this->FlushFileData();
    }
    
    private function FlushFileData()
    {
        $FileHandle = $this->FileHandle;
        if ($FileHandle->flock(LOCK_EX)) {
            $FileHandle->fseek(0, SEEK_SET);
            $FileHandle->ftruncate(0);
            foreach($this->GetFileData() as $Signature => $SerializedExpressionTree) {
                $FileHandle->fputcsv([$Signature, $SerializedExpressionTree]);
            }
            $FileHandle->flock(LOCK_UN);
        }
    }
    
    public function __destruct()
    {
        $this->FileHandle = null;
    }
}
