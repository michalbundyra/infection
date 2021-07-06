<?php
/**
 * This code is licensed under the BSD 3-Clause License.
 *
 * Copyright (c) 2017, Maks Rafalko
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * * Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

declare(strict_types=1);

namespace Infection\Mutator\Unwrap;

use function array_keys;
use function array_slice;
use function count;
use Infection\Mutator\Definition;
use Infection\Mutator\MutatorCategory;
use PhpParser\Node;

/**
 * @internal
 */
final class UnwrapArrayUintersectUassoc extends AbstractUnwrapMutator
{
    public static function getDefinition(): ?Definition
    {
        return new Definition(
            <<<'TXT'
Replaces an `array_uintersect_uassoc` function call with each of its operands. For example:

```php
$x = array_uintersect_uassoc(
    ['foo' => 'bar'],
    ['baz' => 'bar'],
    $value_compare_func,
    $key_compare_func
);
```

Will be mutated to:

```php
$x = ['foo' => 'bar'];
```

And into:

```php
$x = ['baz' => 'bar'];
```

TXT
            ,
            MutatorCategory::SEMANTIC_REDUCTION,
            null,
            <<<'DIFF'
- $x = array_uintersect_uassoc(
-     ['foo' => 'bar'],
-     ['baz' => 'bar'],
-     $value_compare_func,
-     $key_compare_func
- );
# Mutation 1
+ $x = ['foo' => 'bar'];
# Mutation 2
+ $x = ['baz' => 'bar'];
DIFF
        );
    }

    protected function getFunctionName(): string
    {
        return 'array_uintersect_uassoc';
    }

    protected function getParameterIndexes(Node\Expr\FuncCall $node): iterable
    {
        yield from array_slice(
            array_keys($node->args),
            0,
            count($node->args) - 2
        );
    }
}
