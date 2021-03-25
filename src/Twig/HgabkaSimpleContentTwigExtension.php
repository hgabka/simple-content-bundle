<?php

namespace Hgabka\SimpleContentBundle\Twig;

use Hgabka\SimpleContentBundle\Helper\SimpleContentManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HgabkaSimpleContentTwigExtension extends AbstractExtension
{
    /**
     * @var SimpleContentManager
     */
    protected $contentManager;

    /**
     * PublicTwigExtension constructor.
     */
    public function __construct(SimpleContentManager $contentManager)
    {
        $this->contentManager = $contentManager;
    }


    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_simple_content', [$this, 'renderSimpleContent'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string      $name
     * @param array       $params
     * @param string|null $locale
     * @return string
     */
    public function renderSimpleContent(string $name, array $params = [], ?string $locale = null): string
    {
        return $this->contentManager->getContent($name, $params, $locale);
    }
}
