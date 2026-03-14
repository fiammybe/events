<?php

namespace Rector\Tests\TypeDeclaration\Rector\LegacyIcmsModuleToAbstractRector\Fixture;

class ComprehensiveTest
{
    public function testAllScenarios()
    {
        // Assignment - should NOT be transformed
        $icmsModule = icms_getModuleInfo('events');
        
        // Basic usage - should be transformed
        $moduleId = $icmsModule->getVar('mid');
        $moduleName = $icmsModule->getVar('name');
        
        // Method calls - should be transformed
        $icmsModule->displayAdminMenu(-1, 'Title');
        $version = $icmsModule->getInfo('version');
        
        // Property access - should be transformed
        $config = $icmsModule->config;
        $modinfo = $icmsModule->modinfo;
        
        // In function arguments - should be transformed
        $this->processModule($icmsModule->getVar('dirname'));
        
        // In string concatenation - should be transformed
        $url = ICMS_URL . "/modules/" . $icmsModule->getVar('dirname') . "/admin/";
        
        // In array - should be transformed
        $data = [
            'id' => $icmsModule->getVar('mid'),
            'name' => $icmsModule->getVar('name'),
            'module' => $icmsModule
        ];
        
        // In conditions - should be transformed
        if ($icmsModule && $icmsModule->getVar('isactive')) {
            return $icmsModule->getVar('dirname');
        }
        
        // In ternary - should be transformed
        $result = $icmsModule ? $icmsModule->getVar('name') : 'default';
        
        // In while loop - should be transformed
        while ($icmsModule && $icmsModule->getVar('isactive')) {
            break;
        }
        
        // In for loop - should be transformed
        for ($i = 0; $i < $icmsModule->getVar('mid'); $i++) {
            // do something
        }
        
        // Assignment operators - left side should NOT be transformed
        $icmsModule .= '_suffix';
        $icmsModule += 1;
        $icmsModule -= 1;
        $icmsModule *= 2;
        $icmsModule /= 2;
        
        // But right side should be transformed
        $other = $icmsModule->getVar('name');
        
        return $icmsModule->getVar('dirname');
    }

    public function testGlobalVariable()
    {
        global $icmsModule;
        
        // Usage should be transformed
        return $icmsModule->getVar('name');
    }

    public function testParameterUsage($icmsModule)
    {
        // Parameter usage should be transformed
        return $icmsModule->getVar('mid');
    }

    private function processModule($dirname)
    {
        return $dirname;
    }
}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\LegacyIcmsModuleToAbstractRector\Fixture;

class ComprehensiveTest
{
    public function testAllScenarios()
    {
        // Assignment - should NOT be transformed
        $icmsModule = icms_getModuleInfo('events');
        
        // Basic usage - should be transformed
        $moduleId = icms::$module->getVar('mid');
        $moduleName = icms::$module->getVar('name');
        
        // Method calls - should be transformed
        icms::$module->displayAdminMenu(-1, 'Title');
        $version = icms::$module->getInfo('version');
        
        // Property access - should be transformed
        $config = icms::$module->config;
        $modinfo = icms::$module->modinfo;
        
        // In function arguments - should be transformed
        $this->processModule(icms::$module->getVar('dirname'));
        
        // In string concatenation - should be transformed
        $url = ICMS_URL . "/modules/" . icms::$module->getVar('dirname') . "/admin/";
        
        // In array - should be transformed
        $data = [
            'id' => icms::$module->getVar('mid'),
            'name' => icms::$module->getVar('name'),
            'module' => icms::$module
        ];
        
        // In conditions - should be transformed
        if (icms::$module && icms::$module->getVar('isactive')) {
            return icms::$module->getVar('dirname');
        }
        
        // In ternary - should be transformed
        $result = icms::$module ? icms::$module->getVar('name') : 'default';
        
        // In while loop - should be transformed
        while (icms::$module && icms::$module->getVar('isactive')) {
            break;
        }
        
        // In for loop - should be transformed
        for ($i = 0; $i < icms::$module->getVar('mid'); $i++) {
            // do something
        }
        
        // Assignment operators - left side should NOT be transformed
        $icmsModule .= '_suffix';
        $icmsModule += 1;
        $icmsModule -= 1;
        $icmsModule *= 2;
        $icmsModule /= 2;
        
        // But right side should be transformed
        $other = icms::$module->getVar('name');
        
        return icms::$module->getVar('dirname');
    }

    public function testGlobalVariable()
    {
        global $icmsModule;
        
        // Usage should be transformed
        return icms::$module->getVar('name');
    }

    public function testParameterUsage($icmsModule)
    {
        // Parameter usage should be transformed
        return icms::$module->getVar('mid');
    }

    private function processModule($dirname)
    {
        return $dirname;
    }
}

?>
