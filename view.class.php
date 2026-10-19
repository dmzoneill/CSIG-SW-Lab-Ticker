<?php

namespace LabTicker;

function configValue(string $name): mixed
{
    return constant($name);
}

class View
{
    private string $page = "";
    private int $rec_include = 0; // 2 levels of recursion
    private int $rec_replace = 0; // 2 levels of recursion

    public function __construct(string $template = "index")
    {
        $templatePath = (string) configValue('__VIEW') . "/" . $template . ".txt";
        if (!file_exists($templatePath)) {
            return;
        }

        $lines = file($templatePath);
        if ($lines === false) {
            return;
        }

        $this->page = join("", $lines);
        $this->applyIncludes();
    }

    /**
     * @param array<string, mixed> $tags
     */
    public function process(array $tags = array()): void
    {
        if (sizeof($tags) > 0) {
            foreach ($tags as $tag => $data) {
                if (is_array($data)) {
                    $this->processDirective($data[0], $data[1], $data[2]);
                    unset($tags[ $tag ]);
                    $this->process($tags);
                    return;
                } else {
                    $this->replaceInPage("/{" . $tag . "}/", is_scalar($data) ? (string) $data : "");
                }
            }
        }

        if (preg_match_all("/\\{[a-zA-Z0-9_\\.]+\\}/", $this->page, $matches) > 0 && $this->rec_replace < 2) {
            $this->rec_replace++;
            $this->process($tags);
        }
    }

    /**
     * @param mixed $class
     * @param mixed $callback
     */
    private function processDirective(string $match, $class, $callback): void
    {
        $pattern = "/\#$match\#(.*)\#\/$match\#/s";
        $num = preg_match($pattern, $this->page, $matches);
        if ($num > 0) {
            $replacement = call_user_func_array(array( $class , $callback ), array( $matches[1] ));
            if (is_scalar($replacement) || $replacement === null) {
                $this->replaceInPage($pattern, (string) $replacement);
            }
        }
    }

    public function output(): void
    {
        echo $this->page;
    }

    private function resolveInclude(string $file): string
    {
        $local = configValue('__VIEW') . "/" . $file;
        if (file_exists($local)) {
            return $local;
        }

        $shared = configValue('__VIEWFOLDER') . "/shared/" . $file;
        if (file_exists($shared)) {
            return $shared;
        }

        return $local;
    }

    private function loadFile(string $file): string
    {
        $includePath = $this->resolveInclude($file);
        $buffer = file_get_contents($includePath);
        if (!is_string($buffer)) {
            return "";
        }

        return $buffer;
    }

    /**
     * @param non-empty-string $pattern
     */
    private function replaceInPage(string $pattern, string $replacement): void
    {
        $updated = preg_replace($pattern, $replacement, $this->page);
        if (is_string($updated)) {
            $this->page = $updated;
        }
    }

    private function applyIncludes(): void
    {
        preg_match_all("/\\@[a-zA-Z0-9_\\.]+\\@/", $this->page, $matches);

        foreach ($matches as $match) {
            for ($i = 0; $i < count($match); $i++) {
                $include_name = substr($match[$i], 1, strlen($match[$i]) - 2);
                $data = $this->loadFile($include_name . ".txt");
                $this->replaceInPage("/@" . $include_name . "@/", $data);
            }
        }

        if (preg_match_all("/\\@[a-zA-Z0-9_\\.]+\\@/", $this->page, $matches) > 0) {
            if ($this->rec_include < 2) {
                $this->rec_include++;
                $this->applyIncludes();
            }
        }
    }
}
