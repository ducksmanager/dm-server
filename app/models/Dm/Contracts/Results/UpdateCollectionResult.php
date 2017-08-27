<?php
namespace Dm\Contracts\Results;


use Generic\Contracts\Results\GenericReturnObjectInterface;

class UpdateCollectionResult implements GenericReturnObjectInterface
{
    private $action;
    private $numberOfIssues;

    public function __construct($action, $numberOfIssues)
    {
        $this->action = $action;
        $this->numberOfIssues = $numberOfIssues;
    }

    public function toArray() {
        return [
            'action' => $this->action,
            'numberOfIssues' => $this->numberOfIssues
        ];
    }
}