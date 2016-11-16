<?php

namespace DonkeyCode\MailBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DonkeyCodeMailExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.xml');

        $container->getDefinition('donkeycode.mailer')
                ->replaceArgument(2, $config['mail_from'])
                ->replaceArgument(3, $config['reply_to'])
                ->replaceArgument(4, $config['options'])
                ;
    }
}
