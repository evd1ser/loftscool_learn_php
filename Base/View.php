<?php

namespace Base;

class View
{
    const RENDER_TYPE_NATIVE = 1;
    const RENDER_TYPE_TWIG = 2;

    private $_templatePath;
    private $_renderType;
    /** @var \Twig\Environment */
    private $_twig;

    private $_data = [];

    public function __construct($path = '', int $renderType = self::RENDER_TYPE_TWIG)
    {
        $this->_templatePath = $path;
        $this->_renderType = $renderType;
    }

    public function setRenderType(int $renderType)
    {
        if (!in_array($renderType, [self::RENDER_TYPE_NATIVE, self::RENDER_TYPE_TWIG])) {
            // throw new \Exception('Wrong render type: ' . $renderType);
        }
        $this->_renderType = $renderType;
    }

    public function setTemplatePath($path)
    {
        $this->_templatePath = $path;
    }

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
        $this->$name = $value;
    }

    public function __get($name)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        return '';
    }

    /**
     * @return \Twig\Environment
     */
    public function getTwig()
    {
        if (!$this->_twig) {
            $templatePatches = '../App/Views/';

            $loader = new \Twig\Loader\FilesystemLoader($templatePatches);
            $this->_twig = new \Twig\Environment($loader, [
              'cache' => false,
              'autoescape' => false
            ]);
        }

        return $this->_twig;
    }

    public function render($tplName = '')
    {
        switch ($this->_renderType) {

            case self::RENDER_TYPE_NATIVE:
                $path = str_replace('.', DIRECTORY_SEPARATOR, $this->_templatePath);
                $path = trim($path, DIRECTORY_SEPARATOR);

                $tplFileName = '../App/Views/' . $path . '.phtml';
                ob_start(null, null, PHP_OUTPUT_HANDLER_STDFLAGS);
                require $tplFileName;
                return ob_get_clean();
                break;

            case self::RENDER_TYPE_TWIG:
                $twig = $this->getTwig();

                ob_start(null, null, PHP_OUTPUT_HANDLER_STDFLAGS);
                try {
                    $path = str_replace('.', DIRECTORY_SEPARATOR, $this->_templatePath);
                    $path = trim($path, DIRECTORY_SEPARATOR) . '.twig';

                    echo $twig->render($path, $this->_data + ['view' => $this]);
                } catch (\Exception $e) {
                    trigger_error($e->getMessage());
                }
                return ob_get_clean();
                break;
        }

    }
}
