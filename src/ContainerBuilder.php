<?php

namespace Raideer\MagentoIntellisense;

use DI\Container;
use DI\ContainerBuilder as DIContainerBuilder;

final class ContainerBuilder
{
    /**
     * @return Container
     */
    public function build(): Container
    {
        $builder = new DIContainerBuilder();
        $builder->useAutowiring(true);
        $builder->addDefinitions(__DIR__ . '/etc/di.php');
        $container = $builder->build();

        return $container;
    }
}
