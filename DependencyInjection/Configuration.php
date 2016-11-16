<?php

namespace DonkeyCode\MailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('donkey_code_mail');

        $rootNode
            ->children()
                ->scalarNode('mail_from')->defaultNull()->end()
                ->scalarNode('reply_to')->defaultNull()->end()
                ->arrayNode('options')
                    ->info('Colors for default layout args')
                    ->treatNullLike(array())
                    ->useAttributeAsKey('key')
                    ->prototype('scalar')->end()
                    ->defaultValue(array(
                        'header_bg'        => "#2d7cff",
                        'header_txt_color' => "#ffffff",
                        'bg'               => "#efefef",
                        'txt_color'        => "#555555"
                    ))
                ->end();
            ->end()
            ;

        return $treeBuilder;
    }
}
