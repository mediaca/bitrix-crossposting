<?php

declare(strict_types=1);

namespace Mediaca\Crossposting\Template;

class TemplateParser
{
    private array $matches;
    private array $dataCodes;

    public function __construct(private readonly string $template)
    {
        $this->setMatches();
        $this->setDataCodes();
    }

    private function setMatches(): void
    {
        preg_match_all('/\((#([a-z0-9_]+?)#\|?)+\)/i', $this->template, $matches);

        $this->matches = $matches[0];
    }

    private function setDataCodes(): void
    {
        foreach ($this->matches as $i => $match) {
            preg_match_all('/#([a-z0-9_]+?)#/i', $match, $data);

            $this->dataCodes[$i] = $data[1];
        }
    }

    public function getDataCodes(): array
    {
        return array_values(array_unique(array_merge(...$this->dataCodes)));
    }

    public function build(array $values): string
    {
        $result = $this->template;

        foreach ($this->matches as $i => $match) {
            $replace = '';

            foreach ($this->dataCodes[$i] as $code) {
                if ($values[$code] !== null && $values[$code] !== '') {
                    $replace = $values[$code];

                    break;
                }
            }

            $result = str_replace($match, $replace, $result);
        }

        return $result;
    }
}
