<?php

namespace Stp\RedactorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('stp_redactor');
        $rootNode
            ->useAttributeAsKey('id')
            ->prototype('array')
                ->children()
                    ->arrayNode('upload_file')
                        ->info('Allow upload files if present')
                        ->children()
                            ->scalarNode('dir')
                                ->isRequired()
                                ->example("%kernel.root_dir%/../web/uploads/content/images")
                                ->info("Directory upload")
                            ->end()
                            ->scalarNode('maxSize')->defaultValue('5M')->info('Max file size')->end()
                            ->arrayNode('mimeTypes')
                                ->useAttributeAsKey('id')
                                ->info('Mime types allowed for uploading')
                                ->example("['image/png', 'image/jpeg']")
                                ->isRequired()
                                ->prototype('scalar')
                                ->end()
                            ->end()
                            ->booleanNode('folders')
                                ->info('Allow to store file in date-bases folders (like "2012-10-23/file.ext')
                                ->defaultTrue()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('upload_image')
                        ->info('Allow upload images if present')
                        ->children()
                            ->scalarNode('dir')->isRequired()->example("%kernel.root_dir%/../web/uploads/content/images")->end()
                            ->scalarNode('maxSize')->defaultValue('5M')->info('Max file size')->end()
                            ->scalarNode('minWidth')->example(800)->end()
                            ->scalarNode('maxWidth')->example(800)->end()
                            ->scalarNode('minHeight')->example(100)->end()
                            ->scalarNode('maxHeight')->example(100)->end()
                            ->booleanNode('folders')
                                ->info('Allow to store file in date-bases folders (like "2012-10-23/image.png')
                                ->defaultTrue()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('settings')
                        ->info('Redactor settings, see http://imperavi.com/redactor/docs/settings/')
                        ->children()
                            ->scalarNode('lang')->example('en')->end()
                            ->booleanNode('source')->example('false')->end()
                            ->booleanNode('autoresize')->example('false')->end()
                            ->booleanNode('cleanup')->example('false')->end()
                            ->booleanNode('convertLinks')->example('false')->end()
                            ->booleanNode('convertDivs')->example('false')->end()
                            ->booleanNode('overlay')->example('false')->end()
                            ->booleanNode('observeImages')->example('false')->end()
                            ->booleanNode('shortcuts')->example('false')->end()
                            ->booleanNode('air')->example('false')->end()
                            ->booleanNode('mobile')->example('false')->end()
                            ->scalarNode('tabindex')->example(1)->end()
                            ->scalarNode('imageGetJson')->example('/load/imagelist')->end()
                            ->scalarNode('protocol')->example('https://')->end()
                            ->scalarNode('minHeight')->defaultValue(300)->end()
                            ->scalarNode('fileUploadErrorCallback')
                                ->defaultValue('redactorErrorUploadFile')
                                ->info('Name of JS callback function, should be in "window" NS')
                            ->end()
                            ->scalarNode('fileUploadCallback')
                                ->example('redactorUploadFile')
                                ->info('Name of JS callback function, should be in "window" NS')
                            ->end()
                            ->scalarNode('imageUploadErrorCallback')
                                ->defaultValue('redactorErrorUploadFile')
                                ->info('Name of JS callback function, should be in "window" NS')
                            ->end()
                            ->scalarNode('imageUploadCallback')
                                ->example('redactorUploadFile')
                                ->info('Name of JS callback function, should be in "window" NS')
                            ->end()
                            ->arrayNode('buttons')
                                ->useAttributeAsKey('id')
                                ->example("['html', '|', 'formatting', '|', 'bold', 'italic', 'deleted']")
                                ->prototype('scalar')
                                ->end()
                            ->end()
                            ->arrayNode('formattingTags')
                                ->useAttributeAsKey('id')
                                ->example("['p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4']")
                                ->prototype('scalar')
                                ->end()
                            ->end()
                            ->arrayNode('airButtons')
                                ->useAttributeAsKey('id')
                                ->example("['html', '|', 'formatting', '|', 'bold', 'italic', 'deleted']")
                                ->prototype('scalar')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('role')
                        ->info("User roles who have access to this redactor view")
                        ->example("[ROLE_ADMIN, IS_AUTHENTICATED_ANONYMOUSLY]")
                        ->useAttributeAsKey('id')
                        ->prototype('scalar')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
