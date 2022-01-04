<?php

namespace Wysiwyg\OhDear;

class CheckResult
{
    public const STATUS_OK = 'ok';
    public const STATUS_WARNING = 'warning';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CRASHED = 'crashed';
    public const STATUS_SKIPPED = 'skipped';

    public string $name;
    public string $label = '';
    public string $notificationMessage = '';
    public string $shortSummary = '';
    public string $status = '';
    public array  $meta = [];
    /**
     * @param string $name
     * @param string $notificationMessage
     * @param string $shortSummary
     * @param string $status
     * @param array $meta
     *
     * @return self
     */
    public static function make(
        string $name,
        string $notificationMessage = '',
        string $shortSummary = '',
        string $status = self::STATUS_OK,
        array  $meta = []
    ): self {
        return new self($name, '', $notificationMessage, $shortSummary, $status, $meta);
    }

    /**
     * @param string $name
     * @param string $label
     * @param string $notificationMessage
     * @param string $shortSummary
     * @param string $status
     * @param array $meta
     */
    public function __construct(
        string $name,
        string $label = '',
        string $notificationMessage = '',
        string $shortSummary = '',
        string $status = self::STATUS_OK,
        array $meta = []
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->notificationMessage = $notificationMessage;
        $this->shortSummary = $shortSummary;
        $this->status = $status;
        $this->meta = $meta;
    }

    public function notificationMessage(string $notificationMessage): self
    {
        $this->notificationMessage = $notificationMessage;

        return $this;
    }

    public function shortSummary(string $shortSummary): self
    {
        $this->shortSummary = $shortSummary;

        return $this;
    }

    public function status(string $status): self
    {
        $this->status = $status;

        return $this;
    }


    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param array $meta
     *
     * @return $this
     */
    public function meta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'notificationMessage' => $this->notificationMessage,
            'shortSummary' => $this->shortSummary,
            'status' => $this->status,
            'meta' => $this->meta,
        ];
    }
}
