<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\MultipleUploadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Genemu\Bundle\FormBundle\Form\JQuery\DataTransformer\FileToValueTransformer;
use SIP\MultipleUploadBundle\EventListener\FileListener;

/**
 * multiple upload file type
 */
class FileType extends AbstractType
{
    private $rootDir;

    protected $folder;

    /**
     * Constructs
     *
     * @param string $rootDir
     */
    public function __construct($rootDir, $folder)
    {
        $this->rootDir = $rootDir;
        $this->folder = $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new FileListener($this->rootDir, $options['multiple']))
            ->addViewTransformer(new FileToValueTransformer($this->rootDir, $options['folder'], $options['multiple']))
            ->setAttribute('rootDir', $this->rootDir)
        ;
    }


    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $options['attr']['class'] = 'uploaded_files';
        $view->vars = array_replace($view->vars, array(
            'type'             => 'hidden',
            'value'            => $form->getViewData(),
            'multiple'         => $options['multiple'],
            'maxFileSize'      => $options['maxFileSize'],
            'fileTypes'        => $options['fileTypes'],
            'loadHistory'      => $options['loadHistory'],
            'maxNumberOfFiles' => $options['maxNumberOfFiles'],
            'folder'           => $options['folder'],
            'attr'             => $options['attr']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
            'required'         => false,
            'multiple'         => false,
            'maxFileSize'      => 5000000,
            'maxNumberOfFiles' => 5,
            'loadHistory'      => true,
            'fileTypes'        => array('gif', 'jpeg', 'jpg', 'png'),
            'folder'           => $this->folder,
            'data_class'       => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'multiple_upload_file';
    }
}
