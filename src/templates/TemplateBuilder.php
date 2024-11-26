<?php

namespace skygoose\backend_framework\templates;

final class TemplateBuilder
{
    private string $path;
    private string $content;
    private array $placeholders;
    private array $pattern = ['{$', '}'];
    private string $dir;
    public function __construct(string $path, string $templatesDir = "")
    {
        if($templatesDir == "" || $templatesDir == null) { $this->dir = "{$_SERVER['DOCUMENT_ROOT']}/resources/templates/"; }
        else { $this->dir = $templatesDir; }

        $this->path = $path;
        $this->content = file_get_contents($this->dir . $path . ".tpl");
        $this->placeholders = [];
    }

    public function addPlaceholder(string $key, string $value) : TemplateBuilder {
        $this->placeholders[$key] = $value;

        return $this;
    }

    public function addPlaceholders(array $data) : TemplateBuilder {
        foreach ($data as $key => $val) {
            $this->placeholders[$key] = $val;
        }

        return $this;
    }

    public function setPattern(string $start, string $end) : TemplateBuilder {
        $this->pattern[0] = $start;
        $this->pattern[1] = $end;

        return $this;
    }


    public function build(bool $varPreprocess = true, bool $clearComments = true) : string {
        foreach($this->placeholders as $key => $val) {
            $this->content = str_replace($this->pattern[0].$key.$this->pattern[1], $val, $this->content);
        }

        if($varPreprocess) {
            foreach (Template::getGlobalVars() as $k => $v) {
                $this->content = str_replace($k, $v, $this->content);
            }

            $vars = $this->processVarsBlock();
            foreach ($vars as $k => $v) {
                $this->content = str_replace("{%$$k%}", $v, $this->content);
            }
        }

        if($clearComments) {
            $this->clearComments();
        }

        return $this->content;
    }

    public static function of(string $path): TemplateBuilder {
        return new self($path);
    }

    private function clearComments() {
        $pattern = Template::getPattern("COMMENT_PATTERN");
        $this->content = preg_replace($pattern, '', $this->content);
    }
    private function processVarsBlock(): array
    {

        $content = $this->content;

        $pattern = Template::getPattern("VARS_TAG_PATTERN");

        if (preg_match($pattern, $content, $matches)) {
            $varsArray = [];
            $varsContent = $matches[1];
            $varPattern = Template::getPattern("VARS_VAL_PATTERN");

            if (preg_match_all($varPattern, $varsContent, $varMatches, PREG_SET_ORDER)) {
                foreach ($varMatches as $varMatch) {
                    $varName = $varMatch[1];
                    $varValue = trim($varMatch[2], ' "');
                    $varsArray[$varName] = $varValue;
                }
            }

            $content = preg_replace($pattern, '', $content);
            $this->content = $content;
            return $varsArray;
        }
        return [];
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }


}