<?php


namespace CoverCMS\CloudStorage;


class ResultHandler
{
    private $data;

    private $original;

    public function __construct($data, $original = null)
    {
        $this->data = $data;
        $this->original = $original ?: $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed|null
     */
    public function getOriginal()
    {
        return $this->original;
    }
}