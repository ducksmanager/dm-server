<?php
namespace DmServer\DoctrineFunctions;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

class UnixTimestamp extends FunctionNode {
    /**
     * @var $dateExpression PathExpression
     */
    protected $dateExpression;

    /**
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        if ($sqlWalker->getConnection()->getDatabasePlatform() instanceof SqlitePlatform) {
            return 'strftime(\'%s\', '.$this->dateExpression->dispatch($sqlWalker).')';
        }
        else {
            return 'UNIX_TIMESTAMP(' .
                $this->dateExpression->dispatch($sqlWalker) .
                ')';
        }
    }

    /**
     * @param Parser $parser
     * @throws QueryException
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->dateExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
