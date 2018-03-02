<?php

namespace Drupal\oos_base\Controller;

use Drupal\Core\Access\AccessException;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleInstallerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Controller.
 *
 * @package Drupal\oos_base\Controller
 */
class Controller extends ControllerBase {

  /**
   * Module installer.
   *
   * @var ModuleInstallerInterface
   */
  protected $moduleInstaller;

  /**
   * Controller constructor.
   *
   * @param ModuleInstallerInterface $moduleInstaller
   *   Service.
   */
  public function __construct(ModuleInstallerInterface $moduleInstaller) {
    $this->moduleInstaller = $moduleInstaller;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('module_installer'));
  }

  /**
   * Handler to revert default content.
   */
  public function revertContent() {
    try {
      $modules = ['oos_content'];
      $this->moduleInstaller->uninstall($modules);
      $this->getLogger('oos_base')
        ->info('Uninstalled module "oos_content"');
      $this->moduleInstaller->install($modules, TRUE);
      $this->getLogger('oos_base')
        ->info('Installed module "oos_content"');
      return $this->redirect('<front>');
    } catch (\Exception $exception) {
      throw new AccessException();
    }
  }
}