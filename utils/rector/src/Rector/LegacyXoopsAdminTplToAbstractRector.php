<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;
use PhpParser\Node\Expr\Variable;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Transforms legacy $xoopsAdminTpl variable usage to $icmsAdminTpl variable.
 *
 * @see \Utils\Rector\Tests\Rector\LegacyXoopsAdminTplToAbstractRector\LegacyXoopsAdminTplToAbstractRectorTest
 */
final class LegacyXoopsAdminTplToAbstractRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Transform $xoopsAdminTpl variable to $icmsAdminTpl variable', [
            new CodeSample(
                <<<'CODE_SAMPLE'
$xoopsAdminTpl->assign('title', 'Admin Page Title');
if ($xoopsAdminTpl) {
    $xoopsAdminTpl->display('admin_template.html');
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$icmsAdminTpl->assign('title', 'Admin Page Title');
if ($icmsAdminTpl) {
    $icmsAdminTpl->display('admin_template.html');
}
CODE_SAMPLE
            )
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\Expression::class,
            Node\Stmt\If_::class,
            Node\Stmt\ElseIf_::class,
            Node\Stmt\While_::class,
            Node\Stmt\For_::class,
            Node\Stmt\Foreach_::class,
            Node\Stmt\Return_::class,
            Node\Stmt\Switch_::class,
            Node\Stmt\Case_::class,
        ];
    }

    /**
     * @param Node\Stmt\Expression|Node\Stmt\If_|Node\Stmt\ElseIf_|Node\Stmt\While_|Node\Stmt\For_|Node\Stmt\Foreach_|Node\Stmt\Return_|Node\Stmt\Switch_|Node\Stmt\Case_ $node
     */
    public function refactor(Node $node): ?Node
    {
        $hasChanged = false;

        if ($node instanceof Node\Stmt\Expression) {
            return $this->refactorExpression($node);
        }

        $this->traverseNodesWithCallable($node, function (Node $subNode) use (&$hasChanged): ?Node {
            return $this->transformVariable($subNode, $hasChanged);
        });

        return $hasChanged ? $node : null;
    }

    private function refactorExpression(Node\Stmt\Expression $node): ?Node
    {
        $hasChanged = false;

        // Skip transforming left-hand side of assignments
        if ($node->expr instanceof Assign ||
            $node->expr instanceof AssignOp ||
            $node->expr instanceof AssignRef ||
            $node->expr instanceof PreInc ||
            $node->expr instanceof PostInc ||
            $node->expr instanceof PreDec ||
            $node->expr instanceof PostDec) {

            // For assignments, only transform the right-hand side
            if ($node->expr instanceof Assign) {
                $this->traverseNodesWithCallable($node->expr->expr, function (Node $subNode) use (&$hasChanged): ?Node {
                    return $this->transformVariable($subNode, $hasChanged);
                });
            } elseif ($node->expr instanceof AssignOp || $node->expr instanceof AssignRef) {
                $this->traverseNodesWithCallable($node->expr->expr, function (Node $subNode) use (&$hasChanged): ?Node {
                    return $this->transformVariable($subNode, $hasChanged);
                });
            }
        } else {
            // For non-assignment expressions, transform freely
            $this->traverseNodesWithCallable($node->expr, function (Node $subNode) use (&$hasChanged): ?Node {
                return $this->transformVariable($subNode, $hasChanged);
            });
        }

        return $hasChanged ? $node : null;
    }

    private function transformVariable(Node $subNode, bool &$hasChanged): ?Node
    {
        if (!$subNode instanceof Variable) {
            return null;
        }

        if (!$this->isName($subNode, 'xoopsAdminTpl')) {
            return null;
        }

        // Do not change left-hand side of assignments
        $parent = $subNode->getAttribute(AttributeKey::PARENT_NODE);
        if ($parent instanceof Assign && $parent->var === $subNode) {
            return null;
        }
        if ($parent instanceof AssignOp && $parent->var === $subNode) {
            return null;
        }
        if (($parent instanceof PreInc || $parent instanceof PostInc || $parent instanceof PreDec || $parent instanceof PostDec) && $parent->var === $subNode) {
            return null;
        }

        $hasChanged = true;
        return new Variable('icmsAdminTpl');
    }
}

