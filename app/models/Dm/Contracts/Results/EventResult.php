<?php
namespace Dm\Contracts\Results;

use Generic\Contracts\Results\GenericReturnObjectInterface;

class EventResult implements GenericReturnObjectInterface
{
    /** @var string $type */
    private $type;

    /** @var int $secondsDiff */
    private $secondsDiff;

    /** @var int[] $userIds */
    private $userIds;

    /** @var array $data */
    private $data;

    /**
     * Event constructor.
     * @param string $type
     * @param int $secondsDiff
     * @param int[] $userIds
     * @param array $data
     */
    public function __construct(string $type, int $secondsDiff, array $userIds, array $data=[])
    {
        $this->type = $type;
        $this->secondsDiff = $secondsDiff;
        $this->userIds = $userIds;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getSecondsDiff(): int
    {
        return $this->secondsDiff;
    }

    /**
     * @param int $secondsDiff
     */
    public function setSecondsDiff(int $secondsDiff)
    {
        $this->secondsDiff = $secondsDiff;
    }

    /**
     * @return int[]
     */
    public function getUserIds(): array
    {
        return $this->userIds;
    }

    /**
     * @param int[] $userIds
     */
    public function setUserIds(array $userIds)
    {
        $this->userIds = $userIds;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param \stdClass $data
     */
    public function setData(\stdClass $data)
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}