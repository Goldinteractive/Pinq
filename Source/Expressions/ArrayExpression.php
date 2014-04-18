<?php

namespace Pinq\Expressions;

/**
 * Expression representing an array declaration.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayExpression extends Expression
{
    private $KeyExpressions;
    private $ValueExpressions;
    
    public function __construct(array $KeyExpressions, array $ValueExpressions)
    {
        ksort($KeyExpressions);
        ksort($ValueExpressions);
        if (array_keys($KeyExpressions) !== array_keys($ValueExpressions)) {
            throw new \Pinq\PinqException(
                    'The supplied key expression array keys must match the keys of the value expression array: (%s) !== (%s)',
                    implode(', ', array_keys($KeyExpressions)),
                    implode(', ', array_keys($ValueExpressions)));
        }
        $this->KeyExpressions = $KeyExpressions;
        $this->ValueExpressions = $ValueExpressions;
    }

    /**
     * @return Expression|null[]
     */
    public function GetKeyExpressions()
    {
        return $this->KeyExpressions;
    }

    /**
     * @return Expression[]
     */
    public function GetValueExpressions()
    {
        return $this->ValueExpressions;
    }

    public function Traverse(ExpressionWalker $Walker)
    {
        return $Walker->WalkArray($this);
    }

    public function Simplify()
    {
        $KeyExpressions = self::SimplifyAll(array_filter($this->KeyExpressions)) + array_fill_keys(array_keys($this->KeyExpressions), null);
        $ValueExpressions = self::SimplifyAll($this->ValueExpressions);

        if(self::AllOfType($KeyExpressions, ValueExpression::GetType())
                && self::AllOfType($ValueExpressions, ValueExpression::GetType())) {
            $ResolvedArray = [];

            foreach ($KeyExpressions as $ValueKey => $KeyExpression) {
                if($KeyExpression === null) {
                    $ResolvedArray[] = $ValueExpressions[$ValueKey]->GetValue();
                }
                else {
                    $ResolvedArray[$KeyExpression->GetValue()] = $ValueExpressions[$ValueKey]->GetValue();
                }
            }

            return Expression::Value($ResolvedArray);
        }

        return $this->Update(
                $KeyExpressions,
                $ValueExpressions);
    }

    /**
     * @return self
     */
    public function Update(array $KeyExpressions, array $ValueExpressions)
    {
        if ($this->ValueExpressions === $ValueExpressions
                && $this->KeyExpressions === $KeyExpressions) {
            return $this;
        }

        return new self($KeyExpressions, $ValueExpressions);
    }

    protected function CompileCode(&$Code)
    {
        $Code .= '[';
        $First = true;
        foreach ($this->KeyExpressions as $Key => $KeyExpression) {
            if ($First) {
                $First = false;
            } 
            else {
                $Code .= ', ';
            }

            if ($KeyExpression !== null) {
                $KeyExpression->CompileCode($Code);
                $Code .= ' => ';
            }
            $this->ValueExpressions[$Key]->CompileCode($Code);
        }
        $Code .= ']';
    }
    
    public function __clone()
    {
        $this->KeyExpressions = self::CloneAll($this->KeyExpressions);
        $this->ValueExpressions = self::CloneAll($this->ValueExpressions);
    }
}