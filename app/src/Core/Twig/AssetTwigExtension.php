<?php
namespace App\Core\Twig;

use Slim\Container;
use Slim\Views\TwigExtension;

/**
 * Class AssetTwigExtension
 * @package App\Core\Twig
 */
class AssetTwigExtension extends \Twig_Extension
{
    /**
     * @var Container Slim DI Container
     */
    private $dic;

    /**
     * @var TwigExtension Slim Twig Extension (usage
     */
    private $slimTwigExtension;

    /**
     * @var String Relative public folder path
     */
    private $relPublicPath = '/../../../../public/';

    /**
     * TwigAppExtension constructor.
     * Load DIC and TwigExtension
     *
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->dic = $c;
        $this->slimTwigExtension = new TwigExtension($this->dic->get('router'), $this->dic->get('request')->getUri());
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return $this->autoLoadFunctions();
    }

    /**
     * Build an array with Twig_SimpleFunction(s) from each public method of this class ending with "Function"
     *
     * @return array
     */
    private function autoLoadFunctions()
    {
        $twigFunctions = array();
        $modelReflector = new \ReflectionClass(__CLASS__);
        $methods = $modelReflector->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ((($temp = strlen($method->name) - strlen('Function')) >= 0 && strpos($method->name, 'Function',
                    $temp) !== false)
            ) {
                $twigFunctions[] = new \Twig_SimpleFunction(str_replace('Function', '', $method->name),
                    array($this, $method->name));
            }
        }

        return $twigFunctions;
    }

    /**
     * Twig Function to get CSS asset URL
     *
     * @param $filename
     *
     * @return string
     * @throws \Exception
     */
    public function getCssUrlFunction($filename)
    {
        return $this->getAssetUrl('css', $filename);
    }

    /**
     * Twig Function to get JS asset URL
     *
     * @param $filename
     *
     * @return string
     * @throws \Exception
     */
    public function getJsUrlFunction($filename)
    {
        return $this->getAssetUrl('js', $filename);
    }

    /**
     * Private Function to get JS or CSS asset URL
     * Return the .min.css|js file if exists and if ['assets']['min'] settings enabled
     *
     * @param string $ext      Extension filename css|js
     * @param string $filename Filename including subfolders from asset root path
     *
     * @return string Asset URL with website base URL
     * @throws \Exception
     */
    private function getAssetUrl($ext, $filename)
    {
        $settings = $this->dic->get('settings');
        if (isset($settings['assets'], $settings['assets'][$ext . '_url'])) {
            $filePath = __DIR__ . $this->relPublicPath . $settings['assets'][$ext . '_url'] . '/';
            // Check existing original asset file
            if (file_exists($filePath . $filename)) {
                $minFilename = str_replace('.' . $ext, '.min.' . $ext, $filename);

                // Check existing minified asset file
                if (isset($settings['assets']['min']) && $settings['assets']['min'] && isset($settings['assets'][$ext . '_min_url'])) {
                    $minFilePath = __DIR__ . '/../../../../public/' . $settings['assets'][$ext . '_min_url'] . '/';
                    if (file_exists($minFilePath . $minFilename)) {
                        return $this->slimTwigExtension->baseUrl() . '/' . $settings['assets'][$ext . '_min_url'] . '/' . $minFilename;
                    }
                }

                return $this->slimTwigExtension->baseUrl() . '/' . $settings['assets'][$ext . '_url'] . '/' . $filename;
            } else {
                throw new \Exception('Asset "' . $filePath . $filename . '" not found');
            }
        } else {
            throw new \Exception("Missing ['assets']['" . $ext . "_url'] settings");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_extension';
    }
}