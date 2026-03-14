<?php

namespace Rector\Tests\TypeDeclaration\Rector\LegacyIcmsModuleToAbstractRector\Fixture;

class EdgeCases
{
    public function testVariableVariables()
    {
        $icmsModule = icms_getModuleInfo('events');
        $varName = 'icmsModule';
        
        // Variable variables should not be transformed
        $result = $$varName;
        
        // But direct usage should be transformed
        $moduleId = $icmsModule->getVar('mid');
    }

    public function testSimilarVariableNames()
    {
        // These should NOT be transformed
        $icmsModuleHandler = icms::handler('icms_module');
        $icmsModuleConfig = [];
        $myIcmsModule = null;
        
        // Only exact match should be transformed
        $icmsModule = icms_getModuleInfo('events');
        $name = $icmsModule->getVar('name');
    }

    public function testInClosures()
    {
        $icmsModule = icms_getModuleInfo('events');
        
        $callback = function() use ($icmsModule) {
            return $icmsModule->getVar('dirname');
        };
        
        return $callback();
    }

    public function testAsParameter($icmsModule)
    {
        // Parameter usage should be transformed
        return $icmsModule->getVar('mid');
    }

    public function testInForeach()
    {
        $icmsModule = icms_getModuleInfo('events');
        $modules = [$icmsModule];
        
        foreach ($modules as $module) {
            // This should not be transformed (different variable name)
            echo $module->getVar('name');
        }
        
        // But this should be transformed
        echo $icmsModule->getVar('dirname');
    }
}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\LegacyIcmsModuleToAbstractRector\Fixture;

class EdgeCases
{
    public function testVariableVariables()
    {
        $icmsModule = icms_getModuleInfo('events');
        $varName = 'icmsModule';
        
        // Variable variables should not be transformed
        $result = $$varName;
        
        // But direct usage should be transformed
        $moduleId = icms::$module->getVar('mid');
    }

    public function testSimilarVariableNames()
    {
        // These should NOT be transformed
        $icmsModuleHandler = icms::handler('icms_module');
        $icmsModuleConfig = [];
        $myIcmsModule = null;
        
        // Only exact match should be transformed
        $icmsModule = icms_getModuleInfo('events');
        $name = icms::$module->getVar('name');
    }

    public function testInClosures()
    {
        $icmsModule = icms_getModuleInfo('events');
        
        $callback = function() use ($icmsModule) {
            return icms::$module->getVar('dirname');
        };
        
        return $callback();
    }

    public function testAsParameter($icmsModule)
    {
        // Parameter usage should be transformed
        return icms::$module->getVar('mid');
    }

    public function testInForeach()
    {
        $icmsModule = icms_getModuleInfo('events');
        $modules = [icms::$module];
        
        foreach ($modules as $module) {
            // This should not be transformed (different variable name)
            echo $module->getVar('name');
        }
        
        // But this should be transformed
        echo icms::$module->getVar('dirname');
    }
}

?>
