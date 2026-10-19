<?php

namespace LabTicker;

function configValue(string $name): mixed
{
    return constant($name);
}

class View
{
    /** @var string */
    private $page = "";
    /** @var int */
    private $rec_include = 0; // 2 levels of recursion
    /** @var int */
    private $rec_replace = 0; // 2 levels of recursion

    public function __construct(string $template = "index")
    {
        if (file_exists(configValue('__VIEW') . "/" . $template . ".txt")) {
            $this->page = join("", file(configValue('__VIEW') . "/" . $template . ".txt"));
            $this->applyIncludes();
        }
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
                    $this->page = preg_replace("/{" . $tag . "}/", $data, $this->page);
                }
            }
        }

        if (preg_match_all("/\\{[a-zA-Z0-9_\\.]+\\}/", $this->page, $matches) > 0) {
            if ($this->rec_replace < 2) {
                $this->rec_replace++;
                $this->process($tags);
            } else {
                 preg_replace("/\\{[a-zA-Z0-9_\\.]+\\}/", "", $this->page); // cannot find (so replace with nothing)
            }
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

            $this->page = preg_replace($pattern, $replacement, $this->page);
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
        ob_start();
        $includePath = $this->resolveInclude($file);
        /** @psalm-suppress UnresolvableInclude */
        include $includePath;
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    private function applyIncludes(): void
    {
        preg_match_all("/\\@[a-zA-Z0-9_\\.]+\\@/", $this->page, $matches);

        foreach ($matches as $match) {
            for ($i = 0; $i < count($match); $i++) {
                $include_name = substr($match[$i], 1, strlen($match[$i]) - 2);
                $data = $this->loadFile($include_name . ".txt");
                $this->page = preg_replace("/@" . $include_name . "@/", $data, $this->page);
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
