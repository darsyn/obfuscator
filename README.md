# Darsyn Obfuscate

Obfuscate source code and decrypt it during run-time.

This is **not** designed to securely encrypt source code; merely obfuscate
source code to ensure that any extraction of partial and/or full source code has
been done with intent. This can then provide extra evidence in any legal dispute
you have with said party.

This obfuscation method is a proof-of-concept and is **not efficient**
performance-wise.

## Example

```php
<?php declare(strict_types=1);

use Darsyn\Obfuscate\Obfuscate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\TerminableInterface;

require_once __DIR__ . '/vendor/autoload.php';
// The call to the Obfuscator library must come *before* any files that are
// obfuscated are loaded by PHP (such as a call to a class which triggers the
// autoloader), therefore just after including the Autoloader is a good idea.
new Obfuscate([
    'excludePaths' => [
        // You *MUST* exclude the vendor directory from being checked for
        // obfuscated code.
        __DIR__ . '/vendor',
    ],
]);

// This file (the entry script) must not be obfuscated, in order to load
// Autoloaders and the Obfuscator library.
// Now continue your application logic as normal, any further files that are
// loaded will be checked if they are obfuscated and decrypted.
```

## To-do

- Check for efficiency (does OpCache kick in before de-obfuscation on subsequent runs).
- Completely disable cache so that de-obfuscated code isn't saved to the filesystem.
- Allow disabling of original AOP source controllers.
