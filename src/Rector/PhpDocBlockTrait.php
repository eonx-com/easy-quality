<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Comment;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocChildNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;

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

    private function prependPhpDocTagNode(PhpDocInfo $phpDocInfo, PhpDocChildNode $phpDocChildNode): void
    {

    }

    private function updateClassMethodPhpDocBlock(ClassMethod $classMethod): void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($classMethod);

        if (count($this->returnArrayNodeComments) > 0) {
            $newComments = [];
            foreach ($this->returnArrayNodeComments as $nodeComment) {
                foreach (\explode("\n", $nodeComment->getReformattedText()) as $commentText) {
                    if ($commentText && $commentText !== '/**' && $commentText !== ' */') {
                        $commentText = \preg_replace(['/^\/\/\s*/', '/^\s*\*?\s*/'], '', $commentText);
                        $newComments[] = new PhpDocTextNode($commentText);
                    }
                }
            }

            $newComments[] = new PhpDocTextNode('');
            $phpDocInfo->getPhpDocNode()->children = \array_merge(
                $newComments,
                $phpDocInfo->getPhpDocNode()->children
            );
            $phpDocInfo->makeMultiLined();
            $phpDocInfo->markAsChanged();
        }

        $hasReturnTag = false;
        foreach ($phpDocInfo->getPhpDocNode()->children as $child) {
            if ($child instanceof PhpDocTagNode
                && ($child->value instanceof ReturnTagValueNode || $child->value instanceof GenericTypeNode)) {
                if ($child->value->type instanceof GenericTypeNode === false) {
                    $child->value = $this->createReturnIterableMixedType();
                }

                $hasReturnTag = true;
            }
        }

        if ($hasReturnTag === false) {
            $phpDocInfo->addTagValueNode($this->createReturnIterableMixedTag());
        }
    }
}
