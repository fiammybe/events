# RectorPHP Rules for Events Module

This directory contains custom RectorPHP rules for modernizing the Events module codebase.

## LegacyIcmsModuleToAbstractRector

### Purpose
Transforms all occurrences of the `$icmsModule` variable to use `icms::$module` instead, following modern ICMS patterns.

### What it does
- Identifies all variable usages of `$icmsModule` in PHP code
- Replaces them with the static property access `icms::$module`
- Handles all contexts where `$icmsModule` appears (method calls, property access, conditions, etc.)
- Preserves assignment contexts to avoid breaking code functionality
- Includes proper type checking to avoid false positives

### Examples

#### Basic Usage
```php
// Before
$icmsModule = icms_getModuleInfo('events');
$moduleId = $icmsModule->getVar('mid');
$moduleName = $icmsModule->getVar('name');

// After
$icmsModule = icms_getModuleInfo('events');
$moduleId = icms::$module->getVar('mid');
$moduleName = icms::$module->getVar('name');
```

#### Complex Expressions
```php
// Before
$url = ICMS_URL . "/modules/" . $icmsModule->getVar('dirname') . "/";
$name = $icmsModule ? $icmsModule->getVar('name') : 'Unknown';
if ($icmsModule && $icmsModule->getVar('isactive')) {
    return true;
}

// After
$url = ICMS_URL . "/modules/" . icms::$module->getVar('dirname') . "/";
$name = icms::$module ? icms::$module->getVar('name') : 'Unknown';
if (icms::$module && icms::$module->getVar('isactive')) {
    return true;
}
```

#### Assignment Preservation
```php
// Before
$icmsModule = icms_getModuleInfo('events');
$icmsModule = icms::handler("icms_module")->getByDirname('events');
$icmsModule .= '_suffix';
$result = $icmsModule->getVar('dirname');

// After
$icmsModule = icms_getModuleInfo('events');  // Preserved
$icmsModule = icms::handler("icms_module")->getByDirname('events');  // Preserved
$icmsModule .= '_suffix';  // Preserved
$result = icms::$module->getVar('dirname');  // Transformed
```

### Features
- ✅ Transforms variable usage in all contexts
- ✅ Preserves assignments to avoid breaking functionality
- ✅ Handles complex expressions (ternary, concatenation, conditions)
- ✅ Works with method calls and property access
- ✅ Includes comprehensive test coverage
- ✅ Follows RectorPHP best practices

### Usage

To use this rule, add it to your rector.php configuration:

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Utils\Rector\Rector\LegacyIcmsModuleToAbstractRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withRules([
        LegacyIcmsModuleToAbstractRector::class,
    ])
    ->withAutoloadPaths([
        __DIR__ . '/utils/rector/src',
    ]);
```

Then run:
```bash
vendor/bin/rector process --dry-run  # To preview changes
vendor/bin/rector process            # To apply changes
```

### Testing

The rule includes comprehensive test cases covering:
- Basic variable transformations
- Assignment preservation
- Complex expressions
- Edge cases
- Method calls and property access
- Control structures (if, while, for, etc.)

Test files are located in `utils/rector/tests/Rector/LegacyIcmsModuleToAbstractRector/Fixture/`
