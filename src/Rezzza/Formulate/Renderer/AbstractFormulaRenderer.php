<?php

namespace Rezzza\Formulate\Renderer;

use Rezzza\Formulate\Formula;
use Rezzza\Formulate\Exception\RenderFormulaException;

/**
 * AbstractFormulaRenderer
 *
 * @package Formulate
 * @uses FormulaRendererInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
abstract class AbstractFormulaRenderer implements FormulaRendererInterface
{
    protected $options = array(
        'separator_start' => '{{',
        'separator_end' => '}}',
        'strict'        => false,
    );

    /**
     * {@inheritdoc}
     */
    public function render(Formula $formula)
    {
        if (!$token = $formula->getToken()) {
            throw new RenderFormulaException('Token is mandatory');
        }

        $formulaString = $formula->formula;

        $replacements = array();
        foreach ($formula->getSubFormulas() as $key => $value) {
            $replacements[$key] = $value->formula;
        }

        $formulaString = $this->prepare($formulaString);

        $formulaString = $this->replace($formulaString, $this->buildReplacements($replacements));
        $formulaString = $this->replace($formulaString, $this->buildReplacements($token->datas));

        return $formulaString;
    }

    /**
     * @param string $formulaString formulaString
     *
     * @return string
     */
    public function prepare(&$formulaString)
    {
        $start = $this->getOption('separator_start');
        $end   = $this->getOption('separator_end');

        $formulaString = preg_replace(sprintf('/%s[ ]+/', $start), $start, $formulaString);
        $formulaString = preg_replace(sprintf('/[ ]+%s+/', $end), $end, $formulaString);

        return $formulaString;
    }

    /**
     * @param array $replacements replacements
     *
     * @return array
     */
    public function buildReplacements(array $replacements)
    {
        $replacements = array_filter($replacements);

        $datas = array();
        foreach ($replacements as $key => $value) {
            $key = $this->getOption('separator_start').$key.$this->getOption('separator_end');
            $datas[$key] = $value;
        }

        return $datas;
    }

    /**
     * @param string $key     key
     * @param mixed  $default default
     *
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    /**
     * @param string $key   key
     * @param mixed  $value value
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $key;
    }

    /**
     * This method has to be overrided !
     *
     * @param string $formulaString formulaString
     * @param array  $replacements  replacements
     *
     * @return string
     */
    public function replace($formulaString, array $replacements)
    {
        return $formulaString;
    }
}