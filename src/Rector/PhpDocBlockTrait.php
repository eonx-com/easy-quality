<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Comment;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;

trait PhpDocBlockTrait
{
    /**
     * @var Comment[]
     */
    private $returnArrayNodeComments = [];

    private function createReturnIterableMixedTag(): ReturnTagValueNode
    {
        return new ReturnTagValueNode($this->createReturnIterableMixedType(), '');
    }

    private function createReturnIterableMixedType(): GenericTypeNode
    {
        return new GenericTypeNode(
            new IdentifierTypeNode('iterable'), [new IdentifierTypeNode('mixed')]
        );
    }

    private function updateClassMethodPhpDocBlock(ClassMethod $classMethod): void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($classMethod);
        $hasReturnTag = false;
        foreach ($phpDocInfo->getPhpDocNode()->children as $child) {
            if ($child instanceof PhpDocTagNode
                && ($child->value instanceof ReturnTagValueNode || $child->value instanceof GenericTypeNode))
            {
                if ($child->value->type instanceof GenericTypeNode === false) {
                    $child->value = $this->createReturnIterableMixedType();
                }

                $hasReturnTag = true;
            }
        }

        if ($hasReturnTag === false) {
            $phpDocInfo->addTagValueNode($this->createReturnIterableMixedTag());
        }

        if (count($this->returnArrayNodeComments) > 0) {
            $phpDocInfo->addPhpDocTagNode(new PhpDocTextNode(''));
            foreach ($this->returnArrayNodeComments as $nodeComment) {
                foreach (\explode("\n", $nodeComment->getReformattedText()) as $commentText) {
                    if ($commentText && $commentText !== '/**' && $commentText !== ' */') {
                        $commentText = \preg_replace(['/^\/\/\s*/', '/^\s*\*?\s*/'], '', $commentText);

                        $phpDocInfo->addPhpDocTagNode(new PhpDocTextNode($commentText));
                    }
                }
            }
        }
    }
}
