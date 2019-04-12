<?php

namespace Core;

class                           TemplateEngine
{
    /* File informations */
    CONST                       PATH = "src/View";
    CONST                       CACHE_PATH = 'Cache/Template';
    CONST                       EXTENSION = '.tpl';
    CONST                       SUM_EXTENTION = '.sum';
    
    /* Regexp */
    CONST                       OP_EXTENDS = "/{{\s*extends\s+\"\s*(.+)\s*\"\s*}}/";
    
    private                     $_filename;

    public function             __construct($filename)
    {
        if (file_exists($filename) == false)
        {
            throw new \Exception("File \"{$filename}\" not found !");
        }
        $this->_filename = $filename;
    }
   
    static private function     recParse(string $templateName, array $sections = array())
    {
        
        
    }
    
    static public function      parse(string $templateName)
    {
        $aSections = array("Override" => array(), "Current" => array());
        $deep = 0;
        while ($templateName !== null)
        {
            $filename = ROOTPATH . '/' . self::PATH . '/' . "{$templateName}.tpl";
            if (file_exists($filename) == false)
            {
                throw new \Exception("File \"{$filename}\" not found !");
            }
            $buffer = file_get_contents($filename);
            preg_match_all(self::OP_EXTENDS, $buffer, $matches);
            $templateName = (array_key_exists(0, $matches[1])) ? $matches[1][0] : null;
            var_dump($templateName);
            ++$deep;
        }
    }
}