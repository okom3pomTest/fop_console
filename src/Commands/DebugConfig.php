<?php

namespace FOP\Console\Commands;

use Db;
use Configuration;
use FOP\Console\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command is a debugger for the PrestaShop configuration.
 */
final class DebugConfig extends Command
{
    const MAX_LENGTH_CONFIGURATION_VALUE = 100;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('fop:debug:config')
            ->setDescription('Debugger for the PrestaShop configuration')
            ->setHelp('This command displays the configuration of PrestaShop')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Debugger of PrestaShop Configuration');

        Configuration::loadConfiguration();
        $configurations = Db::getInstance()->executeS("SELECT name, value FROM "._DB_PREFIX_."configuration WHERE name <> 'PS_INSTALL_XML_LOADERS_ID'");

        $entries = [];

        foreach($configurations as $configuration) {
            $entries[] = [$configuration['name'], $this->formatValue($configuration['value'])];
        }

        $io->table(
            ['Key', 'Value'],
            $entries
        );

        $io->note('(*) : Value truncated');
    }

    private function formatValue($configurationValue)
    {
        if(strlen($configurationValue) > self::MAX_LENGTH_CONFIGURATION_VALUE) {
            $configurationValue =  substr($configurationValue,0, self::MAX_LENGTH_CONFIGURATION_VALUE).' (*)';
        }

        return $configurationValue;
    }
}
