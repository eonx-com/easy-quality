<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Comment;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\NodeTypeResolver\Node\AttributeKey;

trait PhpDocBlockTrait
{
    /**
     * @var Comment[]
     */
    private $returnArrayNodeComments = [];

    /**
     * @var PhpDocInfo|null
     */
    private $returnArrayNodePhpDocInfo;

    private function updateClassMethodPhpDocBlock(ClassMethod $classMethod): void
    {
        /** @var PhpDocInfo|null $docComment */
        $docComment = $classMethod->getAttribute(AttributeKey::PHP_DOC_INFO);
        $docCommentText = "/**";

        if ($classMethod->getDocComment() === null) {
            $docCommentText .= "\n * @return iterable<mixed>";
        } else {
            foreach ($docComment->getPhpDocNode()->children as $child) {
                if ($child instanceof PhpDocTagNode) {
                    if ($child->value instanceof ReturnTagValueNode) {
                        $iterableTypes = [];
                        if (
                            $child->value->type instanceof GenericTypeNode
                            && $child->value->type->type->name === 'iterable'
                            && isset($child->value->type->genericTypes)
                        ) {
                            foreach ($child->value->type->genericTypes as $genericType) {
                                $iterableTypes[] = $genericType->name;
                            }
                        } else {
                            $iterableTypes[] = 'mixed';
                        }
                        $docCommentText .= "\n * @return iterable<" . \implode(', ', $iterableTypes) . '>';
                    } else {
                        $docCommentText .= "\n * $child->name $child->value";
                    }
                }

                if ($child instanceof PhpDocTextNode) {
                    $docCommentText .= "\n *" . ($child->text ? ' ' . $child->text : '');
                }
            }
        }

        if ($this->returnArrayNodePhpDocInfo !== null
            && count($this->returnArrayNodePhpDocInfo->getPhpDocNode()->children) > 0) {
            $docCommentText .= "\n *";

            foreach ($this->returnArrayNodePhpDocInfo->getPhpDocNode()->children as $child) {
                $commentText = (string)$child;
                $docCommentText .= "\n *" . ($commentText ? ' ' . $commentText : '');
            }
        }

        if (count($this->returnArrayNodeComments) > 0) {
            // $firstLineAdded is really need, do NOT remove
            $firstLineAdded = false;
            foreach ($this->returnArrayNodeComments as $nodeComment) {
                foreach (\explode("\n", $nodeComment->getReformattedText()) as $commentText) {
                    if ($commentText && $commentText !== '/**' && $commentText !== ' */') {
                        if ($firstLineAdded === false) {
                            $docCommentText .= "\n *";
                            $firstLineAdded = true;
                        }
                        $commentText = \preg_replace(['/^\/\/\s*/', '/^\s*\*?\s*/'], '', $commentText);
                        $docCommentText .= "\n *" . ($commentText ? ' ' . $commentText : '');
                    }
                }
            }
        }

        $docCommentText .= "\n */";
        $classMethod->setDocComment(new Doc($docCommentText));
        $docComment = $this->phpDocInfoFactory->createFromNode($classMethod);
        $classMethod->setAttribute(AttributeKey::PHP_DOC_INFO, $docComment);
    }
}
