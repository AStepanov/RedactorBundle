<?php

namespace Stp\RedactorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class StpRedactorExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $envName => $envConfig) {
            foreach(array('file', 'image') as $subKey) {
                $fileKey = 'upload_' . $subKey;
                if (!isset($envConfig[$fileKey]['dir'])) {
                    continue;
                }
                $paths = explode('web', $envConfig[$fileKey]['dir']);
                $envConfig[$fileKey]['web_dir'] = $paths[1];
            }
            /**
             * Clean empty config arrays
             */
            $cleanArrays = array(
                'upload_image'  => array('mimeTypes'),
                'settings'      => array('buttons', 'formattingTags', 'airButtons')
            );
            foreach($cleanArrays as $key => $subKeys) {
                foreach($subKeys as $subKey) {
                    if (isset($envConfig[$key][$subKey]) && !count($envConfig[$key][$subKey])) {
                        unset($envConfig[$key][$subKey]);
                    }
                }
            }

            $container->setParameter(sprintf('stp_redactor.%s', $envName), $envConfig);
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
