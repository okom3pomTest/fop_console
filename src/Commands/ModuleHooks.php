<?php

/**
 * Copyright (c) Since 2020 Friends of Presta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file docs/licenses/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to infos@friendsofpresta.org so we can send you a copy immediately.
 *
 * @author    Friends of Presta <infos@friendsofpresta.org>
 * @copyright since 2020 Friends of Presta
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License ("AFL") v. 3.0
 */

namespace FOP\Console\Commands;

use FOP\Console\Command;
use Module;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ModuleHooks extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('fop:module:hooks')
            ->setDescription('Get modules list')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'module name'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $moduleName = (string) $input->getArgument('name');  /** @phpstan-ignore-line */
        $io = new SymfonyStyle($input, $output);

        if ($module = Module::getInstanceByName($moduleName)) { /** @phpstan-ignore-line */
            $possibleHooksList = $module->getPossibleHooksList();
            $moduleHooks = [];

            foreach ($possibleHooksList as $hook) {
                $isHooked = (int) $module->getPosition($hook['id_hook']);
                if ($isHooked != 0) {
                    $moduleHooks[] = [
                        'name' => $hook['name'],
                        'position' => $isHooked,
                    ];
                }
            }

            if (count($moduleHooks)) {
                $io->title('The module ' . $moduleName . ' is linked on the following hooks :');
                $table = new Table($output);
                $table->setHeaders(['Hook Name', 'Position']);
                foreach ($moduleHooks as $moduleHook) {
                    $table->addRow([$moduleHook['name'], $moduleHook['position']]);
                }
                $table->render();
            } else {
                $io->title('The module is not hooked to any hook');
            }

            return 0;
        } else {
            $io->error('Error the module ' . $moduleName . ' doesn\'t exists');

            return 1;
        }
    }
}
