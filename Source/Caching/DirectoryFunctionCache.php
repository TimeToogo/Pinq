<?php

namespace Pinq\Caching;

use \Pinq\FunctionExpressionTree;

/**
 * Cache implementation for storing the serialized expression trees
 * each in their own file in the specified directory
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class DirectoryFunctionCache implements IFunctionCache
{
    const DefaultExtension = '.cached';
            
    /**
     * The directory to store the files
     * 
     * @var string
     */
    private $Directory;
    
    /**
     * The exension to use for the stored files
     * 
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
