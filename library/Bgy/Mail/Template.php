<?php
/**
 * Bgy Library
 *
 * LICENSE
 *
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 *
 * @category    Bgy
 * @package     Bgy\Mail
 * @subpackage  Template
 * @author      Boris Guéry <guery.b@gmail.com>
 * @license     http://sam.zoy.org/wtfpl/COPYING
 * @link        http://borisguery.github.com/bgylibrary
 */
namespace Bgy\Mail;
use Bgy\Mail\Template;

class Template extends \Zend_Mail
{

    const FORMAT_HTML = 'html';
    const FORMAT_TEXT = 'text';
    const FORMAT_BOTH = 'both';

    /**
     * Obscure name used by view helpers to avoid conflict
     *
     * @var string
     */
    const VAR_SUBJECT = '#INTERNAL#EmailSubject#INTERNAL#';

    /**
     * Obscure name used by view helpers to avoid conflict
     *
     * @var string
     */
    const VAR_SUBJECT_PLACEMENT = '#INTERNAL#EmailSubjectPlacement#INTERNAL#';

    /**
     * The html prefix (ie: phtml) without the leading '.' (dot)
     * @var string
     */
    protected $_htmlSuffix;
    protected static $_defaultHtmlSuffix;

    /**
     * The text prefix (ie: ptxt) without the leading '.' (dot)
     * @var string
     */
    protected $_textSuffix = 'ptxt';
    protected static $_defaultTextSuffix;

    /**
     * @var Zend_Layout
     */
    protected $_layout;
    protected static $_defaultLayout;

    /**
     *
     * @var Zend_View_Interface
     */
    protected $_view;
    protected static $_defaultView;

    /**
     *
     * The path with the view scripts
     * @var string
     */
    protected $_templatePath;
    protected static $_defaultTemplatePath;

    /**
     * The view script name (without the suffix)
     * @var string
     */
    protected $_templateScript;

    /**
     * The format to use
     *     Bgy\Mail\Template::FORMAT_HTML
     *     Bgy\Mail\Template::FORMAT_TEXT
     *     Bgy\Mail\Template::FORMAT_BOTH
     * @var string
     */
    protected $_format;

    /**
     *
     * By default, we send the both format if available
     * @var string
     */
    protected static $_defaultFormat = self::FORMAT_BOTH;

    /**
     *
     * The HTML Renderer to convert Html to Text
     * @var \Bgy\Mail\Template\Html\Renderer
     */
    protected $_htmlRenderer;
    protected static $_defaultHtmlRenderer;

    /**
     *
     * The variables to assign to the view object
     * @var Array
     */
    protected $_viewVariables = array();

    /**
     *
     * Default subject to use for all emails
     * @var string
     */
    protected static $_defaultSubject;

    /**
     *
     * Default subject to use for all emails
     * @var string
     */
    protected static $_defaultSubjectSeparator;

    /**
     *
     * Default subject to use for all emails
     * @var string
     */
    protected $_subjectSeparator;

    /**
     *
     * Convert html to text when the text version is missing
     * @var bool
     */
    protected $_convertHtmlToText;

    /**
     *
     * Available options
     * 'layout'       => Zend_Layout
     * 'view'         => Zend_View
     * 'charset' 	  => 'utf-8'
     * 'htmlSuffix'   => 'phtml'
     * 'textSuffix'   => 'ptxt'
     * 'layoutPath'   => 'layout/scripts'
     * 'layoutScript' => 'layout'
     * 'templatePath' => 'templates/path'
     *
     * @param Zend_Config|Array $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            if (is_array($options)) {
                $this->setOptions($options);
            } elseif ($options instanceof \Zend_Config) {
                $this->setOptions($options->toArray());
            }
        }

        $this->init();
    }

    /**
     * Initialize default variables
     *
     * @return void
     */
    public function init()
    {
        if (null === $this->getHtmlSuffix() && null !== self::getDefaultHtmlSuffix())
        {
            $this->setHtmlSuffixToDefault();
        }

        if (null === $this->getTextSuffix() && null !== self::getDefaultTextSuffix())
        {
            $this->setTextSuffixToDefault();
        }

        if (null === $this->getTemplatePath()) {
            $this->setTemplatePathToDefault();
        }

        if (method_exists($this->getView(), 'addScriptPath')) {
            $this->getView()->addScriptPath($this->getTemplatePath());
        } else {
            $this->getView()->setScriptPath($this->getTemplatePath());
        }

        if (null === $this->getFormat()) {
            $this->setFormatToDefault();
        }

        if (null === $this->getSubjectSeparator()) {
            $this->setSubjectSeparatorToDefault();
        }

        if (null === $this->isConvertHtmlToText()) {
            $this->setConvertHtmlToTextToDefault();
        }
    }

    /**
     * Set Options from an Array
     *
     * @param array $options
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setOptions(Array $options = array())
    {
        if (isset($options['view'])) {
            $view = $options['view'];
            if (is_string($view)) {
                $view = new $view;
            }
        } else {
            $view = new \Zend_View();
        }
        $this->setView($view);
        unset($options['view']);

        if (isset($options['layout'])) {
            $layout = $options['layout'];
            if (is_string($layout)) {
                $layout = new $layout;
            }
        } else {
            $layout = new \Zend_Layout();
        }

        if (isset($options['htmlRenderer'])) {
            $htmlRenderer = $options['htmlRenderer'];
            if (is_string($htmlRenderer)) {
                $htmlRenderer = new $htmlRenderer;
            }
        } else {
            $htmlRenderer = new Template\Html\Renderer\SimpleText();
        }

        $this->setHtmlRenderer($htmlRenderer);
        unset($options['htmlRenderer']);

        $layout->setView($view);
        $this->setLayout($layout);
        unset($options['layout']);

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * Set the charset used by Zend_Mail
     *
     * @param string $charset
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setCharset($charset)
    {
        $this->_charset = $charset;

        return $this;
    }

    /**
     * Set the Renderer used to convert Html template to Text version
     *
     * @param Bgy\Mail\Template\Html\Renderer $renderer
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setHtmlRenderer(Template\Html\Renderer $renderer)
    {
        $this->_htmlRenderer = $renderer;

        return $this;
    }

    /**
     * Return the Html Renderer
     *
     * @return \Bgy\Mail\Template\Html\Renderer
     */
    public function getHtmlRenderer()
    {
        return $this->_htmlRenderer;
    }

    /**
     * Set the Layout Object
     * It must be an instance of Zend_Layout
     *
     * @param \Zend_Layout $layout
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setLayout(\Zend_Layout $layout)
    {
        $this->_layout = $layout;

        return $this;
    }

    /**
     * @return Zend_Layout
     */
    public function getLayout()
    {

        return $this->_layout;
    }

    /**
     * Sets the view object
     * Load helpers paths if found in Zend_Layout instance
     *
     * @param Zend_View_Interface $view
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setView(\Zend_View_Interface $view = null)
    {
        if (\Zend_Layout::getMvcInstance() && ($layoutView = \Zend_Layout::getMvcInstance()->getView())
            && method_exists($layoutView, 'getHelperPaths') && method_exists($view, 'addHelperPath')) {
            $view->addHelperPath(dirname(__FILE__) . '/Template/View/Helper', 'Bgy\Mail\Template\View\Helper\\');
            $helperPaths = $layoutView->getHelperPaths();
            foreach ($helperPaths as $prefix => $paths) {
                foreach ($paths as $path) {
                    $view->addHelperPath($path, $prefix);
                }
            }
        }

        $this->_view = $view;

        return $this;
    }

    /**
     * @return Zend_View
     */
    public function getView()
    {

        return $this->_view;
    }

    /**
     *
     * Sets the default view objec to use
     *
     * @param \Zend_View_Interface $view
     */
    public static function setDefaultView(\Zend_View_Interface $view)
    {
        self::$_defaultView = $view;
    }

    /**
     * Sets the variables to assign to the view object
     * This will clear any previously used variables
     *
     * @param array $variables
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setViewVariables(Array $variables)
    {
        $this->clearViewVariables();
        $this->_viewVariables = $variables;

        return $this;
    }

    /**
     * Add a variable to assign to the view object
     *
     * @param string 	   	  $name
     * @param any 	 Optional $value
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function addViewVariable($name, $value = null)
    {
        $this->_viewVariables[$name] = $value;

        return $this;
    }

    /**
     * Adds views variable to assign to the view object
     *
     * @param array $variables
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function addViewVariables(Array $variables)
    {
        $this->_viewVariables += $variables;

        return $this;
    }

    /**
     * Clear all views variables that will be assigned to the view object
     *
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function clearViewVariables()
    {
        $this->_viewVariables = array();

        return $this;
    }

    /**
     * Gets all the variables that will be assigned to the view object
     *
     * @return array The view variables
     */
    public function getViewVariables()
    {
        return $this->_viewVariables;
    }

    public function send($transport = null)
    {
        $layout = $this->getLayout();
        $this->getView()->assign($this->getViewVariables());

        switch ($this->getFormat()) {
            case self::FORMAT_HTML:
                $processedTemplate = $this->_processTemplate($this->getTemplate(), $this->getHtmlSuffix());
                $this->setBodyHtml($processedTemplate);
                break;

            case self::FORMAT_TEXT:
                $processedTemplate = $this->_processTemplate($this->getTemplate(), $this->getTextSuffix());
                $this->setBodyText($processedTemplate);
                break;

            case self::FORMAT_BOTH:
            default:
                $processedTemplate = $this->_processTemplate($this->getTemplate(), $this->getHtmlSuffix());
                $this->setBodyHtml($processedTemplate);
                $processedTemplate = $this->_processTemplate($this->getTemplate(), $this->getTextSuffix());
                $this->setBodyText($processedTemplate);
                break;
        }

        $this->setSubject($this->_formatSubject());

        parent::send($transport);

        return $this;
    }

    /**
     * Processes the template script, passes through the layout
     *
     * @param string $template
     * @param string $format
     * @return string The processed template
     * @throws Bgy\Mail\Template\Exception
     */
    protected function _processTemplate($template, $format)
    {
        if (!$this->_isTemplateScriptReadable($template . '.' . $format)) {
            throw new Template\Exception('Template \'' . $template . '.' . $format . '\' is not readable or does not exist');
        }
        $this->getLayout()->content = $this->getView()
            ->render($template . '.' . $format);

        $processedTemplate = $this->getLayout()->setViewSuffix($format)
            ->render();
        // we reset the layout
        $this->getLayout()->content = null;

        return $processedTemplate;
    }

    /**
     * Sets if we must convert html to text when the text version is missing
     *
     * @param bool $flag
     */
    public function setConvertHtmlToText($flag)
    {
        $this->_convertHtmlToText = (bool)$flag;

        return $this;
    }

    /**
     * Convert from Html if Text version does not exist?
     *
     * @return boolean
     */
    public function isConvertHtmlToText()
    {
        return (bool)$this->_convertHtmlToText;
    }

    /**
     * Convert from Html if Text version does not exist?
     *
     * @return boolean
     */
    public static function isDefaultConvertHtmlToText()
    {
        return (bool)self::$_defaultConvertHtmlToText;
    }

    /**
     * Sets if we must convert html to text when the text version is missing
     *
     * @param bool $flag
     */
    public static function setDefaultConvertHtmlToText($flag)
    {
       self::$_defaultConvertHtmlToText = (bool)$flag;
    }

    /**
     * Sets if we must convert html to text based on the defaults
     *
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setConvertHtmlToTextToDefault()
    {
        $flag = self::isConvertHtmlToText();
        $this->setConvertHtmlToText($flag);

        return $this;
    }

    /**
     * Appends, Prepends, or Replaces the subject if no subject has been set
     *
     * @return string The subject
     */
    protected function _formatSubject()
    {
        $subject = (null !== $this->getSubject()) ? $this->getSubject() : (string)$this->getDefaultSubject();
        if (null === $this->getSubject()) {
            if ('PREPEND' === $this->getSubjectPlacement()) {
                $subject = $this->getSubjectAddition() . $this->getSubjectSeparator() . $subject;
            } elseif ('APPEND' === $this->getSubjectPlacement()) {
                $subject .= $this->getSubjectSeparator() . $this->getSubjectAddition();
            } elseif ('REPLACE' === $this->getSubjectPlacement()) {
                $subject = $this->getSubjectAddition();
            }
        }

        return $subject;
    }

    /**
     * Gets where we should place the subject addition
     *
     * @return string|null
     */
    public function getSubjectPlacement()
    {
        $placement = null;
        if (isset($this->getView()->{self::VAR_SUBJECT_PLACEMENT})) {
            $placement = $this->getView()->{self::VAR_SUBJECT_PLACEMENT};
        }

        return $placement;
    }

    /**
     * Gets the subject addition to be added to the default subject
     *
     * @return string
     */
    public function getSubjectAddition()
    {
        $addition = '';
        if (isset($this->getView()->{self::VAR_SUBJECT})) {
            $addition = $this->getView()->{self::VAR_SUBJECT};
        }

        return $addition;
    }

    /* (non-PHPdoc)
     * @see Zend_Mail::setSubject()
     */
    public function setSubject($subject)
    {
        // we reset the subject to allow override
        $this->_subject = null;
        parent::setSubject($subject);

        return $this;
    }

    /**
     * Sets the default subject to use
     *
     * @param string $subject
     */
    public static function setDefaultSubject($subject)
    {
         self::$_defaultSubject = $subject;
    }

    /**
     * @return string The default subject
     */
    public static function getDefaultSubject()
    {
        return self::$_defaultSubject;
    }

    /**
     * Clears the default subject
     */
    public static function clearDefaultSubject()
    {
        self::$_defaultSubject = '';
    }

    /**
     * Sets the subject based on the defaults
     *
     * @throws Bgy\Mail\Template\Exception
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setSubjectToDefault()
    {
        $subject = self::$_defautlSubject;
        if (null === $subject) {
            require_once 'Bgy/Mail/Template/Exception.php';
            throw new Template\Exception('No Subject specified');
        }

        return $this;
    }

    /**
     * Sets the default separator used in subject
     * Ex: ' - ', ' | '
     * You must add the space yourself
     *
     * @param string $separator The separator
     */
    public static function setDefaultSubjectSeparator($separator)
    {
         self::$_defaultSubjectSeparator = $separator;
    }

    /**
     * Gets the default subject separator
     *
     * @return string The separator
     */
    public static function getDefaultSubjectSeparator()
    {
        return self::$_defaultSubjectSeparator;
    }

    /**
     * Clears the default subject separator
     */
    public static function clearDefaultSubjectSeparator()
    {
        self::$_defaultSubjectSeparator = '';
    }

    /**
     * Sets the separator in subject
     *
     * @param string $separator
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setSubjectSeparator($separator = '')
    {
        $this->_subjectSeparator = $separator;

        return $this;
    }

    /**
     * Gets the subject separator
     *
     * @return string
     */
    public function getSubjectSeparator()
    {
        return $this->_subjectSeparator;
    }

    /**
     * Sets the default subject separator based on defaults
     *
     * @return \Bgy\Mail\Template Provides fluent interface
     */
    public function setSubjectSeparatorToDefault()
    {
        $separator = self::getDefaultSubjectSeparator();
        $this->setSubjectSeparator($separator);

        return $this;
    }

    /**
     * Checks if a template (view script) exists or is readable
     *
     * @param bool $template
     */
    protected function _isTemplateScriptReadable($template)
    {
        return (bool)$this->getView()->getScriptPath($template);
    }

    /**
     * Retrieves the text version from an Html template using the Html Renderer
     *
     * @return string The text version
     */
    public function getTextFromHtml()
    {
        $layout = $this->getLayout();
        $layout->content = $this->getView()->render($this->getTemplate() . '.' . $this->getHtmlSuffix());
        $resultHtml = $layout->setViewSuffix($this->getHtmlSuffix())->render();

        return $this->getHtmlRenderer()->render($resultHtml);
    }

    /**
     * Sets the template name
     *
     * @param string $template
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setTemplate($template)
    {
        $this->_templateScript = $template;

        return $this;
    }

    /**
     * Gets the template name
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->_templateScript;
    }

    /**
     * Sets the suffix used by Html templates
     *
     * @param string $suffix
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setHtmlSuffix($suffix)
    {
        $this->_htmlSuffix = $suffix;

        return $this;
    }

    /**
     * Gets the suffix used by Html templates
     *
     * @return string
     */
    public function getHtmlSuffix()
    {
        return $this->_htmlSuffix;
    }


    /**
     * Sets the default Html suffix to use
     *
     * @param string $suffix
     */
    public static function setDefaultHtmlSuffix($suffix)
    {
        self::$_defaultHtmlSuffix = $suffix;
    }

    /**
     * Gets the default suffix for Html template
     *
     * @return string|null Either the suffix or null
     */
    public static function getDefaultHtmlSuffix()
    {
        return self::$_defaultHtmlSuffix;
    }

    /**
     * Clears the default Html suffix
     */
    public static function clearDefaultHtmlSuffix()
    {
        self::$_defaultHtmlSuffix = null;
    }

    /**
     * Sets the Html suffix based on the defaults
     *
     * @throws Bgy\Mail\Template\Exception
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setHtmlSuffixToDefault()
    {
        $htmlSuffix = self::$_defaultHtmlSuffix;
        if (null === $htmlSuffix) {
            require_once 'Bgy/Mail/Template/Exception.php';
            throw new Template\Exception('No Html Suffix to use');
        }

        $this->setHtmlSuffix($htmlSuffix);

        return $this;
    }

    /**
     * Sets the suffix used by text template
     *
     * @param unknown_type $suffix
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setTextSuffix($suffix)
    {
        $this->_textSuffix = $suffix;

        return $this;
    }

    /**
     * Gets the suffix used by text template
     *
     * @return string
     */
    public function getTextSuffix()
    {
        return $this->_textSuffix;
    }


    /**
     * Sets the default suffix used by text template
     *
     * @param unknown_type $suffix
     */
    public static function setDefaultTextSuffix($suffix)
    {
        self::$_defaultTextSuffix = $suffix;
    }

    /**
     * Sets the default suffix used by text template
     *
     * @return string|null Either the suffix or null
     */
    public static function getDefaultTextSuffix()
    {
        return self::$_defaultTextSuffix;
    }

    /**
     * Clears the default suffix used by text template
     */
    public static function clearDefaultTextSuffix()
    {
        self::$_defaultTextSuffix = null;
    }

    /**
     * Sets the text suffix based on the defaults
     *
     * @throws Bgy\Mail\Template\Exception
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setTextSuffixToDefault()
    {
        $textSuffix = self::$_defaultTextSuffix;
        if (null === $textSuffix) {
            require_once 'Bgy/Mail/Template/Exception.php';
            throw new Template\Exception('No Text Suffix to use');
        }

        $this->setTextSuffix($textSuffix);

        return $this;
    }

    /**
     * Sets the format to use when sending emails
     * Allowed formats are either:
     * 	- 'html' Html version only
     *  - 'text' Text version only
     *  - 'both' Both text and html version
     *
     * @param string $format
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setFormat($format)
    {
        $this->_format = $format;

        return $this;
    }

    /**
     * Gets the format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * Sets the default format to use when sending emails
     * Allowed formats are either:
     *  - 'html' Html version only
     *  - 'text' Text version only
     *  - 'both' Both text and html version
     *
     * @param string $format
     */
    public static function setDefaultFormat($format)
    {
        self::$_defaultFormat = $format;
    }

    /**
     * Gets the default format
     *
     * @return string
     */
    public static function getDefaultFormat()
    {
        return self::$_defaultFormat;
    }

    /**
     * Sets the format based on the defaults
     *
     * @throws Bgy\Mail\Template\Exception
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setFormatToDefault()
    {
        $format = self::$_defaultFormat;
        if (null === $format) {
            require_once 'Bgy/Mail/Template/Exception.php';
            throw new Template\Exception('No default Format to use');
        }

        return $this;
    }

    /**
     * Clears the default format
     */
    public static function clearDefaultFormat()
    {
        self::$_defaultFormat = null;
    }

    /**
     * Sets the path to templates
     *
     * @param string $path
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setTemplatePath($path)
    {
        $this->_templatePath = $path;

        return $this;
    }

    /**
     * Sets the default path to templates
     *
     * @param string $path
     */
    public static function setDefaultTemplatePath($path)
    {
        self::$_defaultTemplatePath = $path;
    }

    /**
     * Clears the default path to templates
     */
    public static function clearDefaultTemplatePath()
    {
        self::$_defaultTemplatePath = null;
    }

    /**
     * Sets the path to templates based on the defaults
     *
     * @throws Bgy\Mail\Template\Exception
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setTemplatePathToDefault()
    {
        $path = self::$_defaultTemplatePath;
        if (null === $path) {
            require_once 'Bgy/Mail/Template/Exception.php';
            throw new Template\Exception('No template script path set');
        }

        $this->setTemplatePath($path);

        return $this;
    }

    /**
     * Gets the path to templates
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->_templatePath;
    }

    /**
     * Sets the path to layout scripts
     *
     * @param string $path
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setLayoutPath($path)
    {
        $this->getLayout()->setLayoutPath($path);

        return $this;
    }

    /**
     * Gets the path to layout scripts
     *
     * @return string
     */
    public function getLayoutPath()
    {
        return $this->getLayout()->getLayoutPath();
    }

    /**
     * Sets the layout script name
     *
     * @param string $scriptName
     * @return Bgy\Mail\Template Provides fluent interface
     */
    public function setLayoutScript($scriptName)
    {
        $this->getLayout()->setLayout($scriptName);

        return $this;
    }

    /**
     * Gets the layout script name
     *
     * @return string
     */
    public function getLayoutScript()
    {
        return $this->getLayout()->getLayout();
    }
}
