<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\MultipleUploadBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SIPMultipleUploadExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $rootDir = $container->getParameter('kernel.root_dir') . '/../web';
        $uploadDir = $rootDir . '/' . $config['upload_dir'];
        if (!is_dir($uploadDir) && false === @mkdir($uploadDir, 0777, true)) {
            throw new \RuntimeException(sprintf('Could not create upload directory "%s".', $uploadDir));
        }

        $thumbnailsDir = $uploadDir . '/thumbnails';
        if (!is_dir($thumbnailsDir) && false === @mkdir($thumbnailsDir, 0777, true)) {
            throw new \RuntimeException(sprintf('Could not create upload directory "%s".', $thumbnailsDir));
        }

        $container->setParameter('sip_multiple_upload.upload_dir', $uploadDir);
        $container->setParameter('sip_multiple_upload.upload_folder', $config['upload_dir']);
    }
}
