<?php

require_once "./vendor/autoload.php";

$parser = (new \PhpParser\ParserFactory())->createForHostVersion();

$ast = $parser->parse(file_get_contents("./entity.php"));


$traverser = new \PhpParser\NodeTraverser();
$traverser->addVisitor(new class extends \PhpParser\NodeVisitorAbstract {
    public function enterNode(\PhpParser\Node $node) {
        if ($node instanceof \PhpParser\Node\Stmt\Property){
//            print $node-.PHP_EOL;
            foreach ($node->attrGroups as $group){
                foreach ($group->attrs as $attr){
                    if (str_contains($attr->name, "Groups")){
                        print_r($attr);
                    }
                }
            }
            die;
        }
    }
});

$ast = $traverser->traverse($ast);
