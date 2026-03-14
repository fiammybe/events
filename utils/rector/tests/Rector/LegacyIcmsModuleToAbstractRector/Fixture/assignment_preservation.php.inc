<?php

namespace Rector\Tests\TypeDeclaration\Rector\LegacyIcmsModuleToAbstractRector\Fixture;

class AssignmentPreservation
{
    public function testAssignmentPreservation()
    {
        // These assignments should NOT be transformed
        $icmsModule = icms_getModuleInfo('events');
        $icmsModule = icms::handler("icms_module")->getByDirname('events');
        $icmsModule = $this->getModuleInfo();
        
        // But usage after assignment should be transformed
        $moduleId = $icmsModule->getVar('mid');
        $moduleName = $icmsModule->getVar('name');
    }

    public function testReassignment()
    {
        $icmsModule = null;
        $icmsModule = icms_getModuleInfo('events');
        
        // Usage should be transformed
        return $icmsModule->getVar('dirname');
    }
}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\LegacyIcmsModuleToAbstractRector\Fixture;

class AssignmentPreservation
{
    public function testAssignmentPreservation()
    {
        // These assignments should NOT be transformed
        $icmsModule = icms_getModuleInfo('events');
        $icmsModule = icms::handler("icms_module")->getByDirname('events');
        $icmsModule = $this->getModuleInfo();
        
        // But usage after assignment should be transformed
        $moduleId = icms::$module->getVar('mid');
        $moduleName = icms::$module->getVar('name');
    }

    public function testReassignment()
    {
        $icmsModule = null;
        $icmsModule = icms_getModuleInfo('events');
        
        // Usage should be transformed
        return icms::$module->getVar('dirname');
    }
}

?>
