<?php

namespace Hgabka\SimpleContentBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Hgabka\SimpleContentBundle\Entity\SimpleContent;
use Hgabka\SimpleContentBundle\Helper\SimpleContentManager;
use Hgabka\UtilsBundle\Form\WysiwygType;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class SimpleContentAdmin extends AbstractAdmin
{
    /** @var SimpleContentManager */
    protected $manager;

    /** @var Security */
    protected $security;

    /** @var HgabkaUtils */
    protected $utils;
    
    /** @var string|null **/
    protected $editorMode;

    public function setManager(SimpleContentManager $manager): self
    {
        $this->manager = $manager;

        return $this;
    }
    
    public function setEditorMode(?string $editorMode): self
    {
        $this->editorMode = $editorMode;
        
        return $this;
    }

    public function setSecurity(Security $security): self
    {
        $this->security = $security;

        return $this;
    }

    public function setUtils(HgabkaUtils $utils): self
    {
        $this->utils = $utils;

        return $this;
    }

    protected function configureBatchActions(array $actions): array
    {
        return [];
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $alias = current($query->getRootAliases());

        $query->leftJoin($alias.'.translations', 'tr');

        return $query;
    }

    public function preRemove(object $object): void
    {
        $this->manager->deleteContentFromCache($object);
    }

    public function postPersist(object $object): void
    {
        $this->manager->addContentToCache($object);
    }

    public function postUpdate(object $object): void
    {
        $this->manager->addContentToCache($object);
    }

    public function toString($object): string
    {
        return $object->translate($this->utils->getCurrentLocale())->getDescription();
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('export');
        $collection->add('downloadCss', $this->getRouterIdParameter().'/downloadCss');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('search', CallbackFilter::class, [
                'label' => 'hg_simple_content.label.search',
                'callback' => static function (ProxyQueryInterface $query, string $alias, string $field, array $data) {
                    if (empty($data['value'])) {
                        return false;
                    }

                    $query
                        ->andWhere($alias.'.name LIKE :search OR tr.description LIKE :search')
                        ->setParameter('search', '%'.$data['value'].'%')
                    ;

                    return true;
                },
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $list
                ->add('name', null, [
                    'label' => 'hg_simple_content.label.name',
                ])
            ;
        }

        $list
            ->add('description', null, [
                'label' => 'hg_simple_content.label.description',
            ])
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $form
                ->add('name', TextType::class, [
                    'label' => 'hg_simple_content.label.name',
                    'required' => true,
                    'constraints' => new NotBlank(),
                ])
                ->add('css', TextareaType::class, [
                    'label' => 'hg_simple_content.label.css',
                    'required' => false,
                ])
                ->add('cssFiles', TextareaType::class, [
                    'label' => 'hg_simple_content.label.css_files',
                    'required' => false,
                ])
                ->add('width', IntegerType::class, [
                    'label' => 'hg_simple_content.label.width',
                    'required' => false,
                    'constraints' => new GreaterThan(50),
                ])
            ;
        }

        $config = [
            'allowedContent' => true,
            'extraAllowedContent' => '*[*](*){*}',
        ];

        if ($this->getSubject()->getId()) {
            $obj = $this->getSubject();
            if ($obj->getWidth()) {
                $config['width'] = $obj->getWidth();
            }
            $css = $this->getCssFilesArray($obj);
            if (!empty($css)) {
                array_unshift($css, ['/ckeditor/contents.css']);
                $config['contentsCss'] = $css;
            }
        }
        $valueParams = [
            'field_type' => WysiwygType::class,
            'required' => false,
            'config' => $config,
            'label' => 'hg_simple_content.label.value',
        ];

        $editorMode = $this->editorMode;
        if (!empty($editorMode)) {
            $valueParams['attr'] = ['type' => $editorMode];
        }

        $form
            ->add('translations', TranslationsType::class, [
                'label' => false,
                'locales' => $this->manager->getLocales(),
                'required' => false,
                'fields' => [
                    'description' => [
                        'label' => 'hg_simple_content.label.description',
                        'required' => false,
                        'field_type' => TextType::class,
                    ],
                    'value' => $valueParams,
                ],
            ]);
    }

    /**
     * Visszaadja tömbben a simplecontenthez rögzített css fájlokat.
     *
     * @return array
     */
    protected function getCssFilesArray(SimpleContent $scc): array
    {
        $cssFiles = $scc->getCssFiles();
        $cssFiles = empty($cssFiles) ? [] : explode("\n", trim($cssFiles));
        $css = [];

        foreach ($cssFiles as $file) {
            $file = trim($file);
            if ('/' === $file[0] || 0 === strpos($file, 'http://') || 0 === strpos($file, 'https://')) {
                $css[] = $file;
            }
        }

        // Ha szabály is van, akkor a downloadcss action url-je is kell
        $cssRules = $scc->getCss();
        if (!empty($cssRules)) {
            $css[] = $this->generateObjectUrl('downloadCss', $scc);
        }

        return $css;
    }
}
