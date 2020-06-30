<?php


namespace App\Model;


class Statistic
{
    /** @var string $color */
    private $color;

    /** @var string $label */
    private $label;

    /** @var int $value */
    private $value;

    /** @var string $icon */
    private $icon;


    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): Statistic
    {
        $this->color = $color;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): Statistic
    {
        $this->label = $label;
        return $this;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): Statistic
    {
        $this->value = $value;
        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): Statistic
    {
        $this->icon = $icon;
        return $this;
    }
}
