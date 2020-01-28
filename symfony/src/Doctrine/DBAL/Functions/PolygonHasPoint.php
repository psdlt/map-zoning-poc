<?php
declare(strict_types=1);

namespace App\Doctrine\DBAL\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class PolygonHasPoint extends FunctionNode
{
    protected $functionPrototype = '%s @> point(%s, %s)';
    protected $firstNode;
    protected $secondNode;
    protected $thirdNode;

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->firstNode = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondNode = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->thirdNode = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return \sprintf(
            $this->functionPrototype,
            $this->firstNode->dispatch($sqlWalker),
            $this->secondNode->dispatch($sqlWalker),
            $this->thirdNode->dispatch($sqlWalker)
        );
    }
}
