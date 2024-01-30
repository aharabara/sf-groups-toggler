<?php

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

require_once __DIR__ . "/vendor/autoload.php";

/*
 * USAGE: php ./parser ./path/to/php/file.php <propertyName> <list of comma separated groups without spaces>
 * EX: php ./parser ./entity.php slug read,edit,list  -> will add 3 groups
 *     php ./parser ./entity.php slug edit            -> will remove 'edit' group, but will preserve previous 2 groups
 *
 * */

$parser = (new ParserFactory())->createForHostVersion();

$path = $argv[1];
$expectedProp = $argv[2];
$groups = explode(",", $argv[3]);

assert(file_exists($path), 'File does not exists.');

$oldAst = $parser->parse(file_get_contents($path));
$oldTokens = $parser->getTokens();


$traverser = new NodeTraverser();

$traverser->addVisitor(new \PhpParser\NodeVisitor\CloningVisitor());
$newAst = $traverser->traverse($oldAst);


$traverser->addVisitor(new class ($expectedProp, $groups) extends NodeVisitorAbstract {
    private \PhpParser\BuilderFactory $factory;
    private ?Property $currentProperty = null;
    private Standard $printer;
    private bool $groupWasModified = false;

    public function __construct(
        protected $expectedProp,
        protected $groups
    )
    {
        $this->printer = new Standard();
        $this->factory = new \PhpParser\BuilderFactory();

        if (empty($this->expectedProp)) {
            throw new RuntimeException('Property expected.');
        }
        if (empty($this->groups)) {
            throw new RuntimeException('Serialization groups expected.');
        }
    }


    public function enterNode(Node $node)
    {
        if ($this->groupWasModified) {
            return NodeVisitorAbstract::STOP_TRAVERSAL;
        }
        if ($node instanceof Property && $this->currentProperty === null) {
            $propertyName = $node->props[0]->name;
            if ($propertyName->name !== $this->expectedProp) {
                return \PhpParser\NodeVisitor::DONT_TRAVERSE_CHILDREN;
            }
            if (count($node->props) > 1) {
                throw new RuntimeException('Multiple property cases not handled yet.');
            }
            $this->currentProperty = $node;
        }


        return $node;
    }

    public function leaveNode(Node $node): Node|Node\Attribute|Property|int|null
    {
        if ($node instanceof Node\Attribute) {
            //            print $this->printer->prettyPrint([$node]) . PHP_EOL;
            return $this->handleAttribute($node);
        }
        if ($node instanceof Node\AttributeGroup) {
            if(empty($node->attrs)) {
                return NodeVisitor::REMOVE_NODE;
            }
        }

        if ($node === $this->currentProperty && !$this->groupWasModified) {
            $node->attrGroups[] = new Node\AttributeGroup([
                $this->factory->attribute('Groups', [$this->groups])
            ]);
            return $node;
        }

        return $node;
    }

    /**
     * @param Node\Attribute $node
     * @return int|Node\Attribute
     */
    public function handleAttribute(Node\Attribute $node): int|Node\Attribute
    {
        if (!str_contains($node->name, "Groups")) {
            return $node;
        }
        $expr = $node->args[0];
        if (!$expr->value instanceof Array_) {
            throw new RuntimeException('Serializer groups should be specified in an array.');
        }

        if ($this->attributeHasGroup($node, $this->groups)) {
            // @note support constants
            $expr->value->items = array_filter($expr->value->items, function (Node\ArrayItem $item) {
                assert($item->value instanceof String_, 'Group not a string.');
                return !in_array($item->value->value, $this->groups);
            });
            if (empty($expr->value->items)) {
                $this->groupWasModified = true;
                return NodeVisitor::REMOVE_NODE;
            }
        } else {
            array_push($expr->value->items, ...array_map($this->factory->val(...), $this->groups));
        }
        //        $argsAsStr = $this->printer->prettyPrint($node->args);
        //        print "> {$this->expectedProp} #{$node->name}($argsAsStr)" . PHP_EOL;

        $this->groupWasModified = true;

        return $node;
    }

    /**
     * @return true
     */
    public function attributeHasGroup(Node\Attribute $attribute, array $groups): bool
    {
        foreach ($attribute->args as $arg) {
            assert($arg->value instanceof Array_);
            foreach ($arg->value->items as $item) {
                assert($item->value instanceof String_, 'Only "string" groups are supported.');
                if (in_array($item->value->value, $groups)) {
                    return true;
                }
            }
        }

        return false;
    }
});
$prettyPrinter = new Standard();

$oldAst = $traverser->traverse($oldAst);

//print $prettyPrinter->prettyPrint($oldAst, $oldAst, $oldTokens);
file_put_contents($path, $prettyPrinter->printFormatPreserving($oldAst, $oldAst, $oldTokens));
//`php-cs-fixer fix $path &>/dev/null`;
