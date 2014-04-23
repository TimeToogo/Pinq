<?php 

namespace Pinq\Caching;

use Pinq\FunctionExpressionTree;

/**
 * Cache implementation for storing the serialized expression trees
 * each in their own file in the specified directory
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class DirectoryFunctionCache implements IFunctionCache
{
    const DEFAULT_EXTENSION = '.cached';
    
    /**
     * The directory to store the files
     * 
     * @var string
     */
    private $directory;
    
    /**
     * The exension to use for the stored files
     * 
     * @var string
     */
    private $fileExtension;
    
    public function __construct($directory, $fileExtension = self::DEFAULT_EXTENSION)
    {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 511, true)) {
                throw new \Pinq\PinqException(
                        'Invalid cache directory: %s does not exist and could not be created',
                        $directory);
            }
        }
        
        $this->directory = $directory;
        $this->fileExtension = $fileExtension;
    }
    
    private function getCacheFilePath($fileName)
    {
        return $this->directory . DIRECTORY_SEPARATOR . $fileName . $this->fileExtension;
    }
    
    private function getSignaturePath($functionHash)
    {
        return $this->getCacheFilePath(md5($functionHash));
    }
    
    public function save($functionHash, FunctionExpressionTree $functionExpressionTree)
    {
        file_put_contents($this->getSignaturePath($functionHash), serialize($functionExpressionTree));
    }
    
    public function tryGet($functionHash)
    {
        $filePath = $this->getSignaturePath($functionHash);
        
        if (!is_readable($filePath)) {
            return null;
        }
        
        return unserialize(file_get_contents($filePath));
    }
    
    public function clear()
    {
        foreach (glob($this->getCacheFilePath('*')) as $path) {
            unlink($path);
        }
    }
    
    public function remove($functionHash)
    {
        unlink($this->getSignaturePath($functionHash));
    }
}