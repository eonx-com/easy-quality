<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\Reflection\ClassReflection;
use Rector\NodeTypeResolver\Node\AttributeKey;

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

    private function isParentMethodHasDocBlock(Node $classMethod): bool
    {
        $scope = $classMethod->getAttribute(AttributeKey::SCOPE);
        if (!$scope instanceof Scope) {
            // Possibly a trait
            return false;
        }

        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return false;
        }

        /** @var string $methodName */
        $methodName = $this->getName($classMethod->name);

        foreach ($classReflection->getParents() as $parentClassReflection) {
            $nativeClassReflection = $parentClassReflection->getNativeReflection();
            // The class reflection above takes also @method annotations into an account
            if (!$nativeClassReflection->hasMethod($methodName)) {
                continue;
            }

            $parentReflectionMethod = $nativeClassReflection->getMethod($methodName);

            return $parentReflectionMethod->getDocComment() !== false;
        }

        return false;
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
