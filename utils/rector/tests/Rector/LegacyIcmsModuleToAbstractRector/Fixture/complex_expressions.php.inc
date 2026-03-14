<?php

namespace Rector\Tests\TypeDeclaration\Rector\LegacyIcmsModuleToAbstractRector\Fixture;

class ComplexExpressions
{
    public function testComplexExpressions()
    {
        $icmsModule = icms_getModuleInfo('events');
        
        // Function arguments
        $this->someMethod($icmsModule->getVar('mid'));
        
        // String concatenation
        $url = ICMS_URL . "/modules/" . $icmsModule->getVar('dirname') . "/";
        
        // Ternary operator
        $name = $icmsModule ? $icmsModule->getVar('name') : 'Unknown';
        
        // Null coalescing
        $version = $icmsModule->getInfo('version') ?? '1.0';
        
        // Chained method calls
        $result = $icmsModule->getVar('dirname')->toLowerCase();
        
        // Array access with method call
        $config = $icmsModule->config[$icmsModule->getVar('mid')];
    }

    public function testInGlobalContext()
    {
        global $icmsModule;
        
        // Usage of global variable should be transformed
        return $icmsModule->getVar('name');
    }

    private function someMethod($moduleId)
    {
        return $moduleId;
    }
}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\LegacyIcmsModuleToAbstractRector\Fixture;

class ComplexExpressions
{
    public function testComplexExpressions()
    {
        $icmsModule = icms_getModuleInfo('events');
        
        // Function arguments
        $this->someMethod(icms::$module->getVar('mid'));
        
        // String concatenation
        $url = ICMS_URL . "/modules/" . icms::$module->getVar('dirname') . "/";
        
        // Ternary operator
        $name = icms::$module ? icms::$module->getVar('name') : 'Unknown';
        
        // Null coalescing
        $version = icms::$module->getInfo('version') ?? '1.0';
        
        // Chained method calls
        $result = icms::$module->getVar('dirname')->toLowerCase();
        
        // Array access with method call
        $config = icms::$module->config[icms::$module->getVar('mid')];
    }

    public function testInGlobalContext()
    {
        global $icmsModule;
        
        // Usage of global variable should be transformed
        return icms::$module->getVar('name');
    }

    private function someMethod($moduleId)
    {
        return $moduleId;
    }
}

?>
