<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Template;

class TemplateParser
{
    private array $matches;
    private array $data;

    public function __construct(public readonly string $template)
    {
        $this->setMatches();
        $this->setData();
    }

    private function setMatches(): void
    {
        preg_match_all('/\((#([a-z0-9_]+?)#\|?)+\)/i', $this->template, $matches);

        $this->matches = $matches[0];
    }

    private function setData(): void
    {
        foreach ($this->matches as $i => $match) {
            preg_match_all('/#([a-z0-9_]+?)#/i', $match, $data);

            $this->data[$i] = $data[1];
        }
    }

    public function getData(): array
    {
        return array_values(array_unique(array_merge(...$this->data)));
    }
}
