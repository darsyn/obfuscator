# Darsyn Obfuscate

Obfuscate source code and decrypt it during run-time.

This is **not** designed to securely encrypt source code; merely obfuscate
source code to ensure that any extraction of partial and/or full source code has
been done with intent. This can then provide extra evidence in any legal dispute
you have with said party.

This obfuscation method is a proof-of-concept and is **not efficient**
performance-wise.

## Setup

```php
<?php declare(strict_types=1);

use Darsyn\Obfuscate\Obfuscate;

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

## The Magic

The Obfuscator will intercept PHP files as they are loaded and transform
(de-obfuscate) the source code before it is handed over to PHP's parsing engine.

## Example

A PHP file on the filesystem will look like this:

```php
<?php exit('Protected by Darsyn Obfuscator.'); ?>

aFcZG1BjeU4PAhFTEUEQAEM0AhViHgsdTAQyJ08HB1IBAxhFH15rZAFTEU9zCggFGhwcMDtVCwoPFyU2
BkEZDRJPAQQ3Bw0BTAYzJwpAIBoGH0wMAXwtABpUE08fCQYHSW8qCAkYUxJOIEUPElUCGzdPAxETARhM
ER0AFh0XEBwBH1ljCgAXABYcGEUGYh4qU09VUhMQQg8GB0VIIQYKB0kGHQAHARBFGWEQEQoaHE0JYUVZ
AEEVbgBJUwBOT1QAHwAVGwZOVEtUGwwQWEwXCRdEABxLVUMUEUYVHQlUSQYbFgYdDgsbCQkAIB8AFAdF
U3tkT1QAQQBTRUNVUkUATAcYUwQxAEkbVABTUVRSCAQNHhVUHEcEBw0KBl9bCxxUNQ8RExQVAEUGQEJL
Fh0bFw9LUgwAEDpKPRpOWgBHUwdBQVoHSABdRSc8ICBjPyoreT49IXAoIWE6ICYMZ0VBTlQAVE8ALkxY
f1JFTFldbxNp
```

But PHP's parsing engine will receive this:

```php
<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render(':default:index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
        ]);
    }
}
```

## To-do

- [ ] Performance (the bytecode of de-obfuscated source code currently does not get cached in OpCache).
- [ ] Completely disable userland cache so that de-obfuscated code isn't saved to the filesystem.
- [ ] Allow disabling of original AOP source transformers.
