<?php

namespace Pinq\Caching;

use \Pinq\FunctionExpressionTree;

/**
 * Stores the serialized expression trees in a single file in a csv format.
 */
class DirectoryFunctionCache implements IFunctionCache
{
    const DefaultExtension = '.cached';
            
    /**
     * @var string
     */
    private $Directory;
    
    /**
     * @var string
     */
    private $FileExtension;
    
    public function __construct($Directory, $FileExtension = self::DefaultExtension)
    {
        if(!is_dir($Directory)) {
            if(!mkdir($Directory, 0777, true)) {
                throw new \Pinq\PinqException('Invalid cache directory: %s does not exist and could not be created', $Directory);
            }
        }
        
        $this->Directory = $Directory;
        $this->FileExtension = $FileExtension;
    }
    
    private function GetCacheFilePath($FileName)
    {
        return $this->Directory . DIRECTORY_SEPARATOR . $FileName . $this->FileExtension;
    }
    
    private function GetSignaturePath($FunctionHash)
    {
        return $this->GetCacheFilePath(md5($FunctionHash));
    }
    
    public function Save($FunctionHash, FunctionExpressionTree $FunctionExpressionTree)
    {
        file_put_contents($this->GetSignaturePath($FunctionHash), serialize($FunctionExpressionTree));
    }

    public function TryGet($FunctionHash)
    {
        $FilePath = $this->GetSignaturePath($FunctionHash);
        if(!is_readable($FilePath)) {
            return null;
        }
        
        return unserialize(file_get_contents($FilePath));
    }

    public function Clear()
    {
        foreach (glob($this->GetCacheFilePath('*')) as $Path) {
            unlink($Path);
        }
    }

    public function Remove($FunctionHash)
    {
        unlink($this->GetSignaturePath($FunctionHash));
    }
}
