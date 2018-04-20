<?php

declare(strict_types=1);

namespace RulerZ\Pomm\Target;

use Hoa\Ruler\Model as AST;

use RulerZ\Model;
use RulerZ\Target\GenericSqlVisitor;

class PommVisitor extends GenericSqlVisitor
{
    /**
     * {@inheritdoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        return $element->getExpression()->accept($this, $handle, $eldnah);
    }

    /**
     * {@inheritdoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        $handle[] = sprintf('$parameters["%s"]', $element->getName());

        // make it a placeholder
        return '$*';
    }

    /**
     * {@inheritdoc}
     */
    public function visitOperator(AST\Operator $element, &$handle = null, $eldnah = null)
    {
        $parameters = [];
        $operator = $element->getName();
        $sql = parent::visitOperator($element, $parameters, $eldnah);

        if (in_array($operator, ['and', 'or', 'not'], true)) {
            return $sql;
        }

        if ($this->operators->hasOperator($operator)) {
            return sprintf('(new \PommProject\Foundation\Where(%s, [%s]))', $sql, implode(', ', $parameters));
        }

        return sprintf('(new \PommProject\Foundation\Where("%s", [%s]))', $sql, implode(', ', $parameters));
    }
}
