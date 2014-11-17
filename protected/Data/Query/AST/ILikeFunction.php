<?php

namespace SimpleTimeTracker\Data\Query\AST;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Class ILikeFunction.
 * 
 * Doctrine ILIKE function for PostgreSQL.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class ILikeFunction extends FunctionNode
{
	/**
	 * @var Node
	 */
	protected $field;

	/**
	 * @var Node
	 */
	protected $value;

	public function getSql(SqlWalker $sqlWalker)
	{
		return $this->field->dispatch($sqlWalker) . ' ILIKE ' . $this->value->dispatch($sqlWalker);
	}

	public function parse(Parser $parser)
	{
		$parser->match(Lexer::T_IDENTIFIER);
		$parser->match(Lexer::T_OPEN_PARENTHESIS);

		$this->field = $parser->StringExpression();

		$parser->match(Lexer::T_COMMA);

		$this->value = $parser->StringPrimary();

		$parser->match(Lexer::T_CLOSE_PARENTHESIS);
	}

}
