<?php
namespace App\EntityTransform;


use App\Helper\GenericReturnObjectInterface;

class UpdateCollectionResult implements GenericReturnObjectInterface
{
    private $action;
    private $numberOfIssues;

    public function __construct($action, $numberOfIssues)
    {
        $this->action = $action;
        $this->numberOfIssues = $numberOfIssues;
    }

    public function toArray() : array {
        return [
            'action' => $this->action,
            'numberOfIssues' => $this->numberOfIssues
        ];
    }
}